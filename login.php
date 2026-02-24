<?php
session_start();
require 'db/config.php';
require 'otp/mailer.php';

$error = '';

// ✅ If "Remember Me" cookies exist, auto-fill email
$rememberedEmail = $_COOKIE['remember_email'] ?? '';

// ✅ Activity log function
function logActivity($pdo, $user_id, $action, $details = null) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $details]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (!empty($email) && !empty($password)) {

        // ✅ Fetch user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ===========================
        //   ✅ STATUS & PASSWORD CHECK
        // ===========================
        if (!$user) {
            $error = "Invalid email/username or password!";
        } elseif ($user['status'] !== 'Active') {
            $error = "Your account is Inactive. Please contact the CNO.";
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = "Invalid email/username or password!";
        } else {
            // ===========================
            //   ✅ LOGIN CONTINUES HERE
            // ===========================

            // Remember Me cookie
            if ($remember) {
                setcookie('remember_email', $email, time() + (7 * 24 * 60 * 60), "/");
            } else {
                setcookie('remember_email', '', time() - 3600, "/");
            }

            // Device token
            if (empty($_COOKIE['device_token'])) {
                $device_token = bin2hex(random_bytes(16));
                setcookie('device_token', $device_token, time() + (365 * 24 * 60 * 60), "/");
            } else {
                $device_token = $_COOKIE['device_token'];
            }

            // Check if device token exists for this user
            $checkDevice = $pdo->prepare("
                SELECT id FROM login_history 
                WHERE user_id = ? AND device_token = ? 
                LIMIT 1
            ");
            $checkDevice->execute([$user['id'], $device_token]);
            $existingDevice = $checkDevice->fetch(PDO::FETCH_ASSOC);

            $session_id = session_id();
            $browser = $_SERVER['HTTP_USER_AGENT'];
            $ip = $_SERVER['REMOTE_ADDR'];

            if ($existingDevice) {
                // Trusted device → direct login
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_type']  = $user['user_type'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['barangay']   = $user['barangay'];

                // Update login history
                $update = $pdo->prepare("UPDATE login_history SET login_time = NOW(), logout_time = NULL, session_id = ? WHERE id = ?");
                $update->execute([$session_id, $existingDevice['id']]);

                // Update users table session
                $pdo->prepare("UPDATE users SET current_session = ? WHERE id = ?")
                    ->execute([$session_id, $user['id']]);

                logActivity($pdo, $user['id'], "User logged in", "Device token login from IP $ip");

                // Redirect
                header("Location: " . ($user['user_type'] === 'CNO' ? 'cno/home.php' : 'bns/home.php'));
                exit();
            } else {
                // New device → send OTP
                $otp = rand(100000, 999999);
                $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                $stmt = $pdo->prepare("INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['id'], $otp, $expires]);

                // Temp session data for OTP verification
                $_SESSION['pending_user_id']      = $user['id'];
                $_SESSION['pending_user_type']    = $user['user_type'];
                $_SESSION['pending_first_name']   = $user['first_name'];
                $_SESSION['pending_user_email']   = $user['email'];
                $_SESSION['pending_barangay']     = $user['barangay'];
                $_SESSION['pending_device_token'] = $device_token;

                logActivity($pdo, $user['id'], "OTP sent for new device login", "Device token: $device_token, IP: $ip");

                if (sendOTP($user['email'], $otp)) {
                    $_SESSION['otp_message'] = "We sent a One-Time Password (OTP) to your email.";
                } else {
                    $_SESSION['otp_message'] = "Failed to send OTP email. Please contact admin.";
                }

                header("Location: otp/verify_otp.php");
                exit;
            }
        }
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CNO NutriMap | Login</title>
  <link rel="icon" type="image/png" href="img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Brand header -->
  <div class="bg-white shadow py-4 px-6 md:px-10 text-2xl font-bold text-gray-800">
    <span class="text-teal-500">CNO</span> NutriMap
  </div>

  <!-- Main container -->
  <div class="flex flex-1 flex-col md:flex-row">

    <!-- Left panel: Login Form -->
    <div class="md:w-1/2 flex justify-center items-center p-6">
      <div class="w-full max-w-md bg-white shadow-md rounded-lg p-8">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Login</h2>

        <?php if (!empty($error)): ?>
          <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
          <input type="text" name="email" placeholder="Email or Username" 
                 value="<?= htmlspecialchars($rememberedEmail) ?>"
                 class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400" required>

          <div class="relative">
            <input type="password" id="password" name="password" placeholder="Password"
                   class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400 pr-10" required>
            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-600" onclick="togglePassword()">
              <i id="eyeIcon" class="fa-solid fa-eye"></i>
            </span>
          </div>

          <button type="submit" class="w-full bg-teal-500 text-white py-3 rounded-md font-bold hover:bg-teal-600 transition-colors">
            Log In
          </button>

          <div class="flex justify-between items-center text-sm text-gray-600">
            <label class="flex items-center gap-2">
              <input type="checkbox" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?> class="h-4 w-4 rounded border-gray-300">
              Remember me
            </label>
            <a href="index.php" class="text-teal-500 font-semibold hover:underline">Just Visit!</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Right panel: Illustration -->
    <div class="md:w-1/2 flex justify-center items-center p-6 bg-teal-50">
      <img src="img/nutritional.png" alt="Nutrition Illustration" class="max-w-full h-auto rounded-lg shadow-md">
    </div>
  </div>

  <!-- Password toggle script -->
  <script>
    function togglePassword() {
      const passwordField = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if(passwordField.type === "password"){
        passwordField.type = "text";
        eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        passwordField.type = "password";
        eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }
  </script>

</body>
</html>