<?php
// logout.php — destroy session and clear cookie
session_start();

// BRANCHING: only log out if actually logged in
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];

    // Destroy session
    session_unset();
    session_destroy();

    // Clear remember me cookie
    if (isset($_COOKIE['remembered_user'])) {
        setcookie('remembered_user', '', time() - 3600, "/");
    }

    $message = "You have been logged out successfully, Agent " . htmlspecialchars($username) . ".";
} else {
    $message = "You were not logged in.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="3;url=index.php">
</head>
<body>

<section class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center">
        <div style="font-size: 4rem">🔒</div>
        <h1 class="hero-title mt-3">LOGGED <span class="accent">OUT</span></h1>
        <p style="color:#aaa"><?= $message ?></p>
        <p style="color:#666; font-size:0.85rem">Redirecting to home in 3 seconds...</p>
        <a href="index.php" class="btn-cs2 mt-3">Go Home Now</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
