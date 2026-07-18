<?php
// ==========================================
// login.php — Login with Database
// CS2 Knife Wiki
// ==========================================
session_start();
require_once 'db.php';

$current_page = 'login';

// Already logged in? Redirect
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$error_msg   = '';
$success_msg = '';

// ==========================================
// POST: process login
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username    = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password    = isset($_POST['password']) ? $_POST['password']       : '';

    // BRANCHING: validasi input kosong
    if (empty($username) || empty($password)) {
        $error_msg = 'Username and password are required.';
    } else {
        $db = getDB();

        // QUERY: ambil user dari database
        $stmt = $db->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        // BRANCHING: cek password
        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil — set session
            $_SESSION['user']       = $user['username'];
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');

            // COOKIE: remember me 7 hari
            if (isset($_POST['remember_me'])) {
                setcookie('remembered_user', $username, time() + (86400 * 7), '/');
            }

            $success_msg = 'Login successful! Redirecting...';
            header('refresh:1;url=index.php');
        } else {
            $error_msg = 'Invalid username or password.';
        }
    }
}

// Pre-fill dari cookie
$prefill_user = '';
if (isset($_COOKIE['remembered_user'])) {
    $prefill_user = htmlspecialchars($_COOKIE['remembered_user']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="py-5 d-flex align-items-center" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="text-center mb-4">
                        <div style="font-size:3rem">⚔️</div>
                        <h2 class="hero-title" style="font-size: 2rem">AGENT <span class="accent">LOGIN</span></h2>
                        <p class="text-muted" style="font-size:0.85rem">Access the full CS2 Knife Wiki</p>
                    </div>

                    <?php if ($error_msg): ?>
                    <div class="alert-cs2 error"><?= htmlspecialchars($error_msg) ?></div>
                    <?php endif; ?>

                    <?php if ($success_msg): ?>
                    <div class="alert-cs2 success"><?= htmlspecialchars($success_msg) ?></div>
                    <?php endif; ?>

                    <!-- FORM: POST method -->
                    <form method="POST" action="login.php">
                        <div class="form-group-cs2">
                            <label class="form-label-cs2">Username</label>
                            <input
                                type="text"
                                name="username"
                                class="form-input-cs2"
                                placeholder="Enter your username"
                                value="<?= $prefill_user ?>"
                                required
                            >
                        </div>

                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-input-cs2"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <div class="form-check mt-3">
                            <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input" style="background:#1a1a2e;border-color:#8847ff">
                            <label for="remember_me" class="form-check-label" style="color:#ccc;font-size:0.9rem">Remember me for 7 days</label>
                        </div>

                        <button type="submit" class="btn-cs2 w-100 mt-4">LOGIN →</button>
                    </form>

                    <p class="text-center mt-3" style="color:#888; font-size:0.85rem">
                        Don't have an account?
                        <a href="register.php" style="color:#8847ff">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
