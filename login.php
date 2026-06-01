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
  
  <!-- Tailwind Config for Green Theme -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'nutrigreen': {
              500: '#3e9a3e',
              600: '#2e7d32',
              700: '#1b5e20',
            }
          }
        }
      }
    }
  </script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  
  <style>
    /* Force all teal to green */
    .text-teal-500, .text-teal-600, .text-teal-400 {
      color: #3e9a3e !important;
    }
    .bg-teal-500, .bg-teal-600 {
      background-color: #3e9a3e !important;
    }
    .hover\:bg-teal-600:hover {
      background-color: #2e7d32 !important;
    }
    .focus\:ring-teal-400:focus {
      --tw-ring-color: #3e9a3e !important;
    }
    
    body {
      background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
    }
    
    /* Compact card styling */
    .login-card {
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(8px);
      transition: all 0.3s ease;
    }
    .login-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.12);
    }
    
    /* Input styling - compact */
    .input-field {
      transition: all 0.2s ease;
      border: 1.5px solid #e2e8f0;
    }
    .input-field:focus {
      border-color: #3e9a3e;
      box-shadow: 0 0 0 2px rgba(62, 154, 62, 0.1);
      outline: none;
    }
    
    /* Button styling - compact */
    .btn-login {
      background: linear-gradient(135deg, #3e9a3e 0%, #2e7d32 100%);
      transition: all 0.3s ease;
    }
    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 14px -4px rgba(62, 154, 62, 0.35);
    }
    
    /* Illustration panel */
    .illustration-panel {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      position: relative;
      overflow: hidden;
    }
    
    /* Floating animation for illustration */
    .floating-img {
      animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Brand header - Compact -->
  <div class="bg-white shadow-sm py-2.5 px-6 md:px-8">
    <div class="flex items-center gap-2">
      <img src="img/CNO_Logo.png" alt="CNO Logo" class="h-8">
      <span class="text-xl font-bold text-gray-800">
        <span class="text-nutrigreen-500">CNO</span> NutriMap
      </span>
    </div>
  </div>

  <!-- Main container -->
  <div class="flex flex-1 flex-col md:flex-row">

    <!-- Left panel: Login Form - Compact -->
    <div class="md:w-1/2 flex justify-center items-center p-4 md:p-6">
      <div class="w-full max-w-sm login-card bg-white/95 backdrop-blur-sm rounded-xl shadow-md p-5">
        
        <!-- Logo icon - Smaller -->
        <div class="flex justify-center mb-3">
          <div class="w-12 h-12 bg-nutrigreen-100 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-leaf text-nutrigreen-500 text-xl"></i>
          </div>
        </div>
        
        <h2 class="text-xl font-bold mb-1 text-center text-gray-800">Welcome Back</h2>
        <p class="text-gray-500 text-center text-xs mb-4"></p>

        <?php if (!empty($error)): ?>
          <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-3">
            <p class="text-red-600 text-xs text-center"><?= htmlspecialchars($error) ?></p>
          </div>
        <?php endif; ?>

        <form method="POST" class="space-y-3">
          <div>
            <input type="text" name="email" placeholder="Enter your email or username" 
                   value="<?= htmlspecialchars($rememberedEmail) ?>"
                   class="input-field w-full px-3 py-2 rounded-lg bg-gray-50 focus:bg-white transition text-sm" required>
          </div>

          <div>
            <div class="relative">
              <input type="password" id="password" name="password" placeholder="Enter your password"
                     class="input-field w-full px-3 py-2 rounded-lg bg-gray-50 focus:bg-white transition pr-10 text-sm" required>
              <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400 hover:text-nutrigreen-500 transition"
                    onclick="togglePassword()">
                <i id="eyeIcon" class="fa-solid fa-eye text-sm"></i>
              </span>
            </div>
          </div>

          <button type="submit" class="btn-login w-full text-white font-medium py-2 rounded-lg transition-all shadow-sm flex items-center justify-center gap-2 text-sm">
            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
            Log In
          </button>

          <div class="flex justify-between items-center text-xs">
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="checkbox" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?> 
                     class="h-3 w-3 rounded border-gray-300 text-nutrigreen-500 focus:ring-nutrigreen-500">
              <span class="text-gray-600">Remember me</span>
            </label>
            <a href="index.php" class="text-nutrigreen-500 font-semibold hover:underline">Guest →</a>
          </div>
        </form>
        
        <!-- Decorative line -->
        <div class="mt-4 pt-3 text-center">
          <p class="text-[10px] text-gray-400">
            <i class="fa-regular fa-shield-heart mr-0.5"></i> Secure login powered by CNO NutriMap
          </p>
        </div>
      </div>
    </div>

    <!-- Right panel: Illustration - Compact -->
    <div class="md:w-1/2 flex justify-center items-center p-4 md:p-6 illustration-panel">
      <div class="text-center">
        <img src="img/nutritional.png" alt="Nutrition Illustration" 
             class="max-w-full h-auto rounded-xl shadow-lg floating-img max-h-[400px] object-contain">
      </div>
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