<?php
session_start();
header('Content-Type: application/json'); // Return JSON response

require 'db/config.php';
require 'otp/mailer.php';

$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

// ------------------------------
// ✅ Activity log function
// ------------------------------
function logActivity($pdo, $user_id, $action, $details = null) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $details]);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $response['message'] = 'All fields are required!';
            http_response_code(400);
            echo json_encode($response);
            exit;
        }

        // ------------------------------
        // ✅ Fetch user by email or username
        // ------------------------------
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $response['message'] = 'Invalid email/username or password!';
            http_response_code(401);
            echo json_encode($response);
            exit;
        }

        if ($user['status'] !== 'Active') {
            $response['message'] = 'Your account is inactive. Please contact admin.';
            http_response_code(403);
            echo json_encode($response);
            exit;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $response['message'] = 'Invalid email/username or password!';
            http_response_code(401);
            echo json_encode($response);
            exit;
        }

        // ------------------------------
        // ✅ DEVICE TOKEN LOGIC
        // ------------------------------
        if (empty($_COOKIE['device_token'])) {
            $device_token = bin2hex(random_bytes(16));
            setcookie('device_token', $device_token, time() + (365*24*60*60), "/");
        } else {
            $device_token = $_COOKIE['device_token'];
        }

        $session_id = session_id();
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        // Check if this device token exists for this user
        $checkDevice = $pdo->prepare("SELECT id FROM login_history WHERE user_id = ? AND device_token = ? LIMIT 1");
        $checkDevice->execute([$user['id'], $device_token]);
        $existingDevice = $checkDevice->fetch(PDO::FETCH_ASSOC);

        if ($existingDevice) {
            // ------------------------------
            // Trusted device → direct login
            // ------------------------------
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_type']  = $user['user_type'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['email']      = $user['email'];
            $_SESSION['barangay']   = $user['barangay'];

            // Update login history
            $update = $pdo->prepare("UPDATE login_history SET login_time = NOW(), logout_time = NULL, session_id = ? WHERE id = ?");
            $update->execute([$session_id, $existingDevice['id']]);

            // Update user's current session
            $pdo->prepare("UPDATE users SET current_session = ? WHERE id = ?")->execute([$session_id, $user['id']]);

            logActivity($pdo, $user['id'], "User logged in via trusted device", "IP: $ip");

            $response['success'] = true;
            $response['message'] = 'Login successful. Trusted device.';
            $response['redirect'] = ($user['user_type'] === 'CNO') ? 'cno/home.php' : 'bns/home.php';
        } else {
            // ------------------------------
            // New device → send OTP
            // ------------------------------
            $otp = random_int(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            $stmt = $pdo->prepare("INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $otp, $expires]);

            // Store pending session info
            $_SESSION['pending_user_id']      = $user['id'];
            $_SESSION['pending_user_type']    = $user['user_type'];
            $_SESSION['pending_first_name']   = $user['first_name'];
            $_SESSION['pending_user_email']   = $user['email'];
            $_SESSION['pending_barangay']     = $user['barangay'];
            $_SESSION['pending_device_token'] = $device_token;

            logActivity($pdo, $user['id'], "OTP sent for new device login", "Device token: $device_token, IP: $ip");

            if (sendOTP($user['email'], $otp)) {
                $response['success'] = true;
                $response['message'] = 'OTP sent successfully!';
                $response['redirect'] = 'otp/verify_otp.php';
            } else {
                $response['message'] = 'Failed to send OTP email. Please try again.';
                http_response_code(500);
            }
        }

    } else {
        $response['message'] = 'Invalid request method!';
        http_response_code(405);
    }

} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);