<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

// ✅ Require login
  if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user info
$stmt = $pdo->prepare("SELECT first_name, last_name, username, phone_number, email, address, barangay, profile_pic 
                       FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users 
                           SET first_name = ?, last_name = ?, username = ?, phone_number = ?, email = ?, address = ?
                           WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $username, $phone, $email, $address, $user_id]);

    header("Location: profile.php?updated=1");
    exit();
}

$profile_pic = "../uploads/default_profile.png";
if (!empty($user['profile_pic']) && file_exists("../uploads/" . $user['profile_pic'])) {
    $profile_pic = "../uploads/" . htmlspecialchars($user['profile_pic']);
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BNS | Edit Profile</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  background: #f5f5f5;
}

.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.page-title {
  font-size: 22px;
  font-weight: bold;
  margin: 20px 30px 10px;
  color: #333;
}

.body-layout {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px 30px;
  gap: 20px;
}

.profile-card {
  background: #fff;
  border-radius: 12px;
  padding: 30px 20px;
  flex: 0 0 350px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.profile-pic-wrapper {
  position: relative;
  width: 180px;
  height: 180px;
  margin-bottom: 15px;
  align-self: center;
}

.profile-pic-wrapper img {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #eee;
}

.change-pic {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: #009688;
  color: white;
  border-radius: 50%;
  width: 34px;
  height: 34px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  font-size: 16px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.3);
  transition: background 0.2s;
}

.change-pic:hover {
  background: #00796b;
}

.info-card {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  flex: 1;
  min-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 5px; /* small vertical spacing for consistency */
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.info-card h3 {
  margin: 0 0 15px;
  font-size: 18px;
  font-weight: bold;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}

.info-group {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 15px; /* ✅ adds comfortable space between each input */
}

.info-group:last-child {
  margin-bottom: 0; /* removes extra space after the last field */
}

.info-group label {
  min-width: 130px;
  font-weight: bold;
  font-size: 14px;
  color: #333;
  display: flex;
  align-items: center;
  gap: 6px;
}

.info-group input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background: #fff;
  font-size: 15px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 25px; /* ✅ more space before the buttons */
}

.btn {
  background: #009688;
  color: white;
  padding: 10px 18px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 15px;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease-in-out;
}

.btn:hover {
  background: #00796b;
}

.btn.cancel {
  background: #aaa;
}

.btn.cancel:hover {
  background: #888;
}

  </style>
</head>
<body>
  <div class="layout">
    <div class="page-title">Edit Profile</div>

    <div class="body-layout">
      <div class="profile-card">
        <div class="profile-pic-wrapper">
          <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
          <div class="change-pic" title="Change Profile Picture" onclick="document.getElementById('profileInput').click()">
            <i class="fa-solid fa-camera"></i>
          </div>
          <form id="uploadForm" method="POST" enctype="multipart/form-data" action="upload_profile.php">
            <input type="file" name="profile_pic" id="profileInput" accept="image/*" onchange="document.getElementById('uploadForm').submit();" hidden>
          </form>
        </div>
        <h3><?php echo htmlspecialchars($user['username']); ?></h3>
        <p><i class="fa-solid fa-location-dot"></i> Barangay: <?php echo htmlspecialchars($user['barangay']); ?></p>
      </div>

      <div class="info-card">
        <h3>Update Information</h3>
        <form method="POST">
          <div class="info-group">
            <label><i class="fa-regular fa-user"></i> First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
          </div>
          <div class="info-group">
            <label><i class="fa-regular fa-user"></i> Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
          </div>
          <div class="info-group">
            <label><i class="fa-solid fa-user-tag"></i> Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
          </div>
          <div class="info-group">
            <label><i class="fa-solid fa-phone"></i> Phone:</label>
            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
          </div>
          <div class="info-group">
            <label><i class="fa-regular fa-envelope"></i> Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <div class="info-group">
            <label><i class="fa-solid fa-map-marker-alt"></i> Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
          </div>

          <div class="actions">
            <a href="profile.php" class="btn cancel">Cancel</a>
            <button type="submit" class="btn">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
