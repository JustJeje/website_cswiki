<?php
// ==========================================
// register.php — User Registration
// CS2 Knife Wiki
// ==========================================
session_start();
require_once 'db.php';

$current_page = 'register';

// Already logged in? Redirect
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$error_msg   = '';
$success_msg = '';

// FUNCTION: validate username (alphanumeric + underscore, 3–20 chars)
function isValidUsername(string $u): bool {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $u);
}

// ==========================================
// POST: process registration
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = isset($_POST['username'])  ? trim($_POST['username'])  : '';
    $email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';
    $password  = isset($_POST['password'])  ? $_POST['password']        : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2']       : '';

    // BRANCHING: validasi input
    if (empty($username) || empty($password) || empty($password2)) {
        $error_msg = 'Username and password are required.';
    } elseif (!isValidUsername($username)) {
        $error_msg = 'Username must be 3–20 characters (letters, numbers, underscore only).';
    } elseif (strlen($password) < 6) {
        $error_msg = 'Password must be at least 6 characters.';
    } elseif ($password !== $password2) {
        $error_msg = 'Passwords do not match.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Invalid email format.';
    } else {
        $db = getDB();

        // BRANCHING: cek apakah username sudah ada
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_msg = 'Username already taken. Choose another.';
        } else {
            // Hash password + INSERT ke database
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2  = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "user")');
            $stmt2->bind_param('sss', $username, $email, $hashed);

            if ($stmt2->execute()) {
                $success_msg = 'Account created! Redirecting to login...';
                header('refresh:2;url=login.php');
            } else {
                $error_msg = 'Registration failed. Please try again.';
            }
            $stmt2->close();
        }
        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CS2 Knife Wiki</title>
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
                        <div style="font-size:3rem">🎖️</div>
                        <h2 class="hero-title" style="font-size:2rem">CREATE <span class="accent">ACCOUNT</span></h2>
                        <p class="text-muted" style="font-size:0.85rem">Join the CS2 Knife Wiki community</p>
                    </div>

                    <?php if ($error_msg): ?>
                    <div class="alert-cs2 error"><?= htmlspecialchars($error_msg) ?></div>
                    <?php endif; ?>

                    <?php if ($success_msg): ?>
                    <div class="alert-cs2 success"><?= htmlspecialchars($success_msg) ?></div>
                    <?php endif; ?>

                    <!-- FORM: register -->
                    <form method="POST" action="register.php">

                        <div class="form-group-cs2">
                            <label class="form-label-cs2">Username <span style="color:#e4432d">*</span></label>
                            <input
                                type="text"
                                name="username"
                                class="form-input-cs2"
                                placeholder="3–20 chars, no spaces"
                                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                                required
                            >
                        </div>

                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Email <span style="color:#666">(optional)</span></label>
                            <input
                                type="email"
                                name="email"
                                class="form-input-cs2"
                                placeholder="your@email.com"
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                            >
                        </div>

                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Password <span style="color:#e4432d">*</span></label>
                            <input
                                type="password"
                                name="password"
                                class="form-input-cs2"
                                placeholder="Min. 6 characters"
                                required
                            >
                        </div>

                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Confirm Password <span style="color:#e4432d">*</span></label>
                            <input
                                type="password"
                                name="password2"
                                class="form-input-cs2"
                                placeholder="Repeat your password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn-cs2 w-100 mt-4">REGISTER →</button>
                    </form>

                    <p class="text-center mt-3" style="color:#888; font-size:0.85rem">
                        Already have an account?
                        <a href="login.php" style="color:#8847ff">Login here</a>
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
