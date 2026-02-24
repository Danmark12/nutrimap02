<?php
session_start();
require '../db/config.php';
require 'mailer.php';

// Redirect if no pending user
if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$user_id = $_SESSION['pending_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ------------------------
    // RESEND OTP
    // ------------------------
    if (isset($_POST['resend'])) {
        $otp = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $pdo->prepare("INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, ?)")
            ->execute([$user_id, $otp, $expires]);

        sendOTP($_SESSION['pending_user_email'], $otp);
        $_SESSION['otp_message'] = "A new OTP has been sent.";

    // ------------------------
    // VERIFY OTP
    // ------------------------
    } else {
        $otp = trim($_POST['otp']);

        $stmt = $pdo->prepare("SELECT * FROM otp_codes WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $code = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($code && $code['otp_code'] === $otp && strtotime($code['expires_at']) > time()) {
            
            // OTP verified → login user
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $_SESSION['pending_user_type'];
            $_SESSION['first_name'] = $_SESSION['pending_first_name'];
            $_SESSION['email'] = $_SESSION['pending_user_email'];
            $_SESSION['barangay'] = $_SESSION['pending_barangay'];

            // ------------------------
            // LOGIN HISTORY
            // ------------------------
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $session_id = session_id();
            $device_token = $_SESSION['pending_device_token'] ?? bin2hex(random_bytes(16));

            // Check if this device already exists
            $checkStmt = $pdo->prepare("SELECT id FROM login_history WHERE user_id = ? AND device_token = ?");
            $checkStmt->execute([$user_id, $device_token]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                // Insert new record
                $insertStmt = $pdo->prepare("
                    INSERT INTO login_history (user_id, session_id, browser, ip_address, device_token, login_time)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $insertStmt->execute([$user_id, $session_id, $userAgent, $ip, $device_token]);
            } else {
                // Update existing record
                $updateStmt = $pdo->prepare("
                    UPDATE login_history SET session_id = ?, browser = ?, ip_address = ?, login_time = NOW()
                    WHERE id = ?
                ");
                $updateStmt->execute([$session_id, $userAgent, $ip, $existing['id']]);
            }

            // Save device token in session for future trusted login
            $_SESSION['device_token'] = $device_token;

            // ------------------------
            // CLEAN UP PENDING SESSION
            // ------------------------
            unset(
                $_SESSION['pending_user_id'],
                $_SESSION['pending_user_type'],
                $_SESSION['pending_first_name'],
                $_SESSION['pending_user_email'],
                $_SESSION['pending_barangay'],
                $_SESSION['pending_device_token']
            );

            // Redirect based on user type
            if ($_SESSION['user_type'] === 'CNO') {
                header("Location: ../cno/home.php");
            } else {
                header("Location: ../bns/home.php");
            }
            exit();

        } else {
            $error = "Invalid or expired OTP!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Verify OTP</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-sm">
    <h2 class="text-2xl font-semibold text-center mb-6">Enter OTP</h2>

    <?php if (isset($_SESSION['otp_message'])): ?>
      <p class="text-green-600 mb-4 text-center"><?php echo $_SESSION['otp_message']; ?></p>
      <?php unset($_SESSION['otp_message']); ?>
    <?php endif; ?>

    <?php if ($error): ?>
      <p class="text-red-600 mb-4 text-center"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="otp" maxlength="6" placeholder="Enter OTP"
             class="w-full p-3 border border-gray-300 rounded text-center text-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
      <button type="submit" 
              class="w-full bg-green-600 text-white p-3 rounded hover:bg-green-700 transition-colors">
        Verify
      </button>
    </form>

    <form method="POST" class="mt-4">
      <button type="submit" name="resend" 
              class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition-colors">
        Resend OTP
      </button>
    </form>
  </div>
</body>
</html>