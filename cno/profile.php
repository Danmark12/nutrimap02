<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

// ✅ Require login
  if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// ✅ Fetch user info (with profile_pic)
$stmt = $pdo->prepare("SELECT first_name, last_name, username, phone_number, email, address, barangay, profile_pic 
                       FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// ✅ Prepare variables safely
$full_name = htmlspecialchars($user['first_name'] . " " . $user['last_name']);
$username = htmlspecialchars($user['username']);
$phone = htmlspecialchars($user['phone_number']);
$email = htmlspecialchars($user['email']);
$address = htmlspecialchars($user['address']);
$barangay = htmlspecialchars($user['barangay']);

// ✅ Profile picture (use default if null or empty)
$profile_pic = "../uploads/default_profile.png";
if (!empty($user['profile_pic']) && file_exists("../uploads/" . $user['profile_pic'])) {
    $profile_pic = "../uploads/" . htmlspecialchars($user['profile_pic']);
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CNO | Profile</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f5f5f5; }
    .layout { display: flex; flex-direction: column; min-height: 100vh; }
    .page-title { font-size: 22px; font-weight: bold; margin: 20px 30px 10px; color: #333; }
    .body-layout { flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 20px 30px; gap: 20px; }

    /* ✅ PROFILE CARD */
    .profile-card {
      background: #fff;
      border-radius: 12px;
      padding: 30px 20px;
      flex: 0 0 350px; /* made wider */
      display: flex;
      flex-direction: column;
      align-items: flex-start; /* left align text */
      justify-content: flex-start;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .profile-pic-wrapper {
      position: relative;
      width: 180px; /* bigger picture */
      height: 180px;
      margin-bottom: 15px;
      align-self: center; /* keep picture centered */
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
    .change-pic:hover { background: #00796b; }

    .profile-card h3 {
      margin: 10px 0 5px;
      font-size: 20px;
      font-weight: bold;
      color: #333;
      text-align: left;
    }
    .profile-card p.barangay {
      font-size: 15px;
      color: #666;
      display: flex;
      align-items: center;
      gap: 6px;
      margin: 0;
      text-align: left;
    }

    /* ✅ INFO CARD */
    .info-card {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      flex: 1;
      min-width: 400px; /* makes it longer */
      display: flex;
      flex-direction: column;
      gap: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .info-card h3 { margin: 0 0 10px; font-size: 18px; font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 8px; }
    .info-group { display: flex; align-items: center; gap: 10px; }
    .info-group label { min-width: 130px; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 6px; color: #333; }
    .info-group input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #f9f9f9; font-size: 14px; color: #555; }
    .edit-profile { margin-top: auto; display: flex; justify-content: flex-end; }
    .edit-profile a {
      background: #009688;
      color: white;
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: background 0.2s ease-in-out;
    }
    .edit-profile a:hover { background: #00796b; }
    #uploadForm { display: none; }
  </style>
</head>
<body>
  <div class="layout">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    <div class="page-title">Profile</div>

    <div class="body-layout">
      <!-- ✅ PROFILE CARD -->
      <div class="profile-card">
        <div class="profile-pic-wrapper">
          <img id="profileImage" src="<?php echo $profile_pic; ?>" alt="Profile Picture">
          <div class="change-pic" title="Change Profile Picture" onclick="document.getElementById('profileInput').click()">
            <i class="fa-solid fa-camera"></i>
          </div>
          <form id="uploadForm" method="POST" enctype="multipart/form-data" action="upload_profile.php">
            <input type="file" name="profile_pic" id="profileInput" accept="image/*" onchange="document.getElementById('uploadForm').submit();">
          </form>
        </div>
        <h3><?php echo $username; ?></h3>
        <p class="barangay">
          <i class="fa-solid fa-location-dot"></i> Barangay: <?php echo $barangay; ?>
        </p>
      </div>

      <!-- ✅ INFO CARD -->
      <div class="info-card">
        <h3>Basic Information</h3>
        <div class="info-group">
          <label><i class="fa-regular fa-user"></i> Full Name:</label>
          <input type="text" value="<?php echo $full_name; ?>" disabled>
        </div>
        <div class="info-group">
          <label><i class="fa-solid fa-phone"></i> Phone:</label>
          <input type="text" value="<?php echo $phone; ?>" disabled>
        </div>
        <div class="info-group">
          <label><i class="fa-regular fa-envelope"></i> Email:</label>
          <input type="email" value="<?php echo $email; ?>" disabled>
        </div>
        <div class="info-group">
          <label><i class="fa-solid fa-map-marker-alt"></i> Address:</label>
          <input type="text" value="<?php echo $address; ?>" disabled>
        </div>
        <div class="edit-profile">
          <a href="edit_profile.php"><i class="fa-solid fa-pen"></i> Edit Profile</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
