<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BaC System - Welcome</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width:520px;">

        <h1>BaC Student System</h1>
        <p class="subtitle">University Grade Management Portal</p>

        <div class="welcome-options">

            <a href="index.php" class="welcome-option">
                <div class="welcome-title">Student Login</div>
                <div class="welcome-desc">Sign in with your student account</div>
            </a>

            <a href="index.php?admin=1" class="welcome-option">
                <div class="welcome-title">Admin Login</div>
                <div class="welcome-desc">Sign in with an administrator account</div>
            </a>

            <a href="register.php" class="welcome-option">
                <div class="welcome-title">Register</div>
                <div class="welcome-desc">Create a new student account</div>
            </a>

        </div>

        <p style="text-align:center; margin-top:24px; font-size:12px; color:#9ca3af;">
            &copy; <?= date('Y') ?> BaC System
        </p>

    </div>
</div>

</body>
</html>