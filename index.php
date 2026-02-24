<?php
// index.php

// ✅ Optional: Start a session (recommended if your home.php uses session data)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Automatically redirect to your homepage
// (You can use require if you want to directly load its content instead of redirect)
header("Location: landing_page/home.php");
exit();
?>
