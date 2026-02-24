<?php
session_start();
require '../db/config.php';

// ✅ Require login
  if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
    $filePath = $uploadDir . $fileName;

    // ✅ Validate file type (only images allowed)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['profile_pic']['type'], $allowedTypes)) {
        die("Invalid file type. Please upload JPG, PNG, or GIF.");
    }

    // ✅ Move file to uploads folder
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filePath)) {
        // ✅ Save file name in database
        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->execute([$fileName, $user_id]);

        // ✅ Redirect back to profile page
        header("Location: profile.php");
        exit();
    } else {
        die("Error uploading file.");
    }
} else {
    die("No file uploaded.");
}
