<?php
session_start();
require '../db/config.php';

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : null;
    $user_type = $_POST['user_type']; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($user_type === 'CNO') {
        $barangay = 'CNO';
    }

    if ($password !== $confirm_password) {
        $message = "⚠️ Passwords do not match!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users 
            (first_name, last_name, username, email, phone_number, address, barangay, user_type, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$first_name, $last_name, $username, $email, $phone_number, $address, $barangay, $user_type, $hash]);
            $message = "✅ Account created successfully!";
            
                // ✅ Log activity
            if (isset($_SESSION['user_id'])) {
                $creator_id = $_SESSION['user_id']; // the one creating the account
                $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
                $logStmt->execute([
                    $creator_id,
                    "Created new account: {$first_name} {$last_name} ({$username}) - Role: {$user_type}, Barangay: {$barangay}"
                ]);
            }

            // Redirect to users.php after successful insert
            header("Location: users.php");
            exit();
            
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $message = "⚠️ Username or Email already exists!";
            } else {
                $message = "❌ Error: " . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | Create Account</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<style>
    body{
        margin:0;
        font-family:Arial, sans-serif;
        background:#f4f6f9;
    }
    .topbar{
        background:#fff;
        padding:12px 20px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        border-bottom:1px solid #ddd;
    }
    .topbar h2{
        margin:0;
        font-size:18px;
        color:#0d9488;
    }
    .topbar input[type="text"]{
        padding:5px 10px;
        width:250px;
        border-radius:6px;
        border:1px solid #ccc;
    }
    .container{
        display:flex;
        justify-content:center;
        align-items:center;
        margin-top:40px;
    }
    .card{
        background:#fff;
        padding:30px;
        border-radius:12px;
        box-shadow:0 4px 10px rgba(0,0,0,0.1);
        width:420px;
    }
    .card h2{
        text-align:center;
        margin-bottom:20px;
        font-size:24px;
    }
    form{
        display:flex;
        flex-direction:column;
        gap:15px;
    }
    .row{
        display:flex;
        gap:10px;
    }
    input, select{
        padding:10px;
        border:1px solid #ccc;
        border-radius:8px;
        width:100%;
        box-sizing:border-box;
    }
    button{
        background:#0d9488;
        color:#fff;
        padding:12px;
        border:none;
        border-radius:8px;
        font-size:16px;
        cursor:pointer;
    }
    button:hover{
        background:#0b7a70;
    }
    p.message{
        text-align:center;
        color:red;
        margin-bottom:10px;
    }
    .relative{
        position:relative;
    }
.toggle-eye{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
    user-select:none;
}
</style>
</head>
<body>



<div class="container">
    <div class="card">

        <div style="margin-bottom:15px;">
        <a href="users.php" style="display:inline-block; text-decoration:none; background: #02ad9fff; color:#fff; padding:8px 16px; border-radius:6px; font-size:14px;" onmouseover="this.style.background='#4b5563'" onmouseout="this.style.background='#6b7280'">← Back</a>
    </div>
        <h2>Create Account</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="row">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="phone_number" placeholder="Phone No." required>
            </div>

            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="address" placeholder="Address" required>

            <div class="row">
                <select name="barangay" id="barangay" required>
                    <option value="">Select Barangay</option>
                    <option value="Amoros">Amoros</option>
                    <option value="Bolisong">Bolisong</option>
                    <option value="Cogon">Cogon</option>
                    <option value="Himaya">Himaya</option>
                    <option value="Hinigdaan">Hinigdaan</option>
                    <option value="Kalabaylabay">Kalabaylabay</option>
                    <option value="Molugan">Molugan</option>
                    <option value="Bolobolo">Bolobolo</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Kibonbon">Kibonbon</option>
                    <option value="Sambulawan">Sambulawan</option>
                    <option value="Calongonan">Calongonan</option>
                    <option value="Sinaloc">Sinaloc</option>
                    <option value="Taytay">Taytay</option>
                    <option value="Ulaliman">Ulaliman</option>
                </select>

                <select name="user_type" id="user_type" required onchange="toggleBarangay()">
                    <option value="">Select User Type</option>
                    <option value="BNS">BNS</option>
                    <option value="CNO">CNO</option>
                </select>
            </div>

            <!-- Password fields with closed-eye by default -->
            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-eye" data-target="password">🙈</span>
            </div>

            <div class="relative">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-eye" data-target="confirm_password">🙈</span>
            </div>

            <button type="submit">Add User</button>
        </form>
    </div>
</div>

<script>
function toggleBarangay() {
    const userType = document.getElementById("user_type").value;
    const barangaySelect = document.getElementById("barangay");
    if (userType === "CNO") {
        barangaySelect.value = "CNO";
        barangaySelect.disabled = true;
    } else {
        barangaySelect.disabled = false;
    }
}

// Toggle password visibility + icon
document.querySelectorAll('.toggle-eye').forEach(icon=>{
    icon.addEventListener('click', ()=>{
        const input = document.getElementById(icon.dataset.target);
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '👁️';   // open eye
        } else {
            input.type = 'password';
            icon.textContent = '🙈';   // closed eye
        }
    });
});
</script>

</body>
</html>
