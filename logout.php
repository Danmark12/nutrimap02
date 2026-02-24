<?php
// logout.php
session_start();
require 'db/config.php';

// ✅ Log activity before destroying the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $details = "Trusted Device"; // Always store this instead of actual device info

    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, details, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, "Logged out", $details]);
    } catch (PDOException $e) {
        // Optional: hide error in production
        echo "Error logging activity: " . $e->getMessage();
    }
}

// ✅ Destroy session after logging
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
