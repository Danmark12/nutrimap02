<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

// ✅ Only allow CNO (admin) to view profiles
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

// ✅ Get user_id from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$user_id = intval($_GET['id']);

// ✅ Fetch user info
$stmt = $pdo->prepare("SELECT first_name, last_name, username, phone_number, email, address, barangay, profile_pic, password_hash 
                       FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// ✅ Prepare variables safely
$full_name = htmlspecialchars($user['first_name'] . " " . $user['last_name']);
$username  = htmlspecialchars($user['username']);
$phone     = htmlspecialchars($user['phone_number']);
$email     = htmlspecialchars($user['email']);
$address   = htmlspecialchars($user['address']);
$barangay  = htmlspecialchars($user['barangay']);
$current_password = htmlspecialchars($user['password_hash']); // hashed password

// ✅ Profile picture
$profile_pic = "../uploads/default_profile.png";
if (!empty($user['profile_pic']) && file_exists("../uploads/" . $user['profile_pic'])) {
    $profile_pic = "../uploads/" . htmlspecialchars($user['profile_pic']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CNO | View Profile</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f5f5f5; }
    .layout { display: flex; flex-direction: column; min-height: 100vh; }
    .page-title { font-size: 22px; font-weight: bold; margin: 20px 30px 10px; color: #333; display: flex; justify-content: space-between; align-items: center; }
    .back-btn { padding: 8px 14px; background: #007bff; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; }
    .back-btn:hover { background: #0056b3; }
    .body-layout { flex: 1; display: flex; justify-content: center; align-items: flex-start; padding: 20px 30px; gap: 20px; }

    /* PROFILE CARD */
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
    .profile-pic-wrapper { width: 180px; height: 180px; margin-bottom: 15px; align-self: center; }
    .profile-pic-wrapper img { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 3px solid #eee; }
    .profile-card h3 { margin: 10px 0 5px; font-size: 20px; font-weight: bold; color: #333; }
    .profile-card p.barangay { font-size: 15px; color: #666; display: flex; align-items: center; gap: 6px; margin: 0; }

    /* INFO CARD */
    .info-card {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      flex: 1;
      min-width: 400px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .info-card h3 { margin: 0 0 10px; font-size: 18px; font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 8px; }
    .info-group { display: flex; align-items: center; gap: 10px; }
    .info-group label { min-width: 130px; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 6px; color: #333; }
    .info-group input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #f9f9f9; font-size: 14px; color: #555; }
    .change-btn { padding: 8px 14px; background: #28a745; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .change-btn:hover { background: #1e7e34; }

    /* MODAL */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 20px; border-radius: 10px; width: 400px; }
    .modal-header { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
    .modal-content input { width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; }
    .cancel-btn { padding: 8px 14px; background: #6c757d; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
    .cancel-btn:hover { background: #5a6268; }
  </style>
</head>
<body>
  <div class="layout">
    <div class="page-title">
      View Profile
      <a href="javascript:history.back()" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a>
    </div>

    <div class="body-layout">
      <!-- PROFILE CARD -->
      <div class="profile-card">
        <div class="profile-pic-wrapper">
          <img src="<?php echo $profile_pic; ?>" alt="Profile Picture">
        </div>
        <h3><?php echo $username; ?></h3>
        <p class="barangay">
          <i class="fa-solid fa-location-dot"></i> Barangay: <?php echo $barangay; ?>
        </p>
      </div>

      <!-- INFO CARD -->
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
        <hr>
        <h3>Password</h3>
        <button class="change-btn" onclick="openModal()">Change Password</button>
      </div>
    </div>
  </div>

  <!-- MODAL -->
  <div class="modal" id="passwordModal">
    <div class="modal-content">
      <div class="modal-header">Change Password</div>
      <form method="POST" action="update_password.php">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
          <button type="submit" class="change-btn">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('passwordModal').style.display = 'flex';
    }
    function closeModal() {
      document.getElementById('passwordModal').style.display = 'none';
    }
  </script>
</body>
</html>
