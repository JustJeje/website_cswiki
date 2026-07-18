<?php
// navbar.php — shared navigation component
$nav_links = [
    ["href" => "index.php",   "label" => "Home",    "icon" => "🏠"],
    ["href" => "knives.php",  "label" => "Knives",  "icon" => "🔪"],
    ["href" => "compare.php", "label" => "Compare", "icon" => "⚖️"],
    ["href" => "quiz.php",    "label" => "Quiz",    "icon" => "❓"],
    ["href" => "contact.php", "label" => "Contact", "icon" => "📩"],
    ["href" => "about.php",   "label" => "About",   "icon" => "ℹ️"],
];
$current_file = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg cs2-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand cs2-brand" href="index.php">
            <img src="images/logo.png" alt="CS2 Knife Wiki Logo" class="nav-logo-img"
                 onerror="this.style.display='none'">
            <span class="nav-logo-text"><span class="accent">CS2</span> KNIFE WIKI</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-1 align-items-center">
                <?php foreach ($nav_links as $link):
                    $active = ($current_file === $link['href']) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link cs2-nav-link <?= $active ?>" href="<?= $link['href'] ?>">
                        <?= $link['icon'] ?> <?= $link['label'] ?>
                    </a>
                </li>
                <?php endforeach; ?>

                <?php if (isset($_SESSION['user']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link cs2-nav-link <?= $current_file === 'admin.php' ? 'active' : '' ?>"
                       href="admin.php" style="color:#fbbf24">
                        ⚙️ Admin
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item ms-2">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="logout.php" class="btn-nav-logout">Logout</a>
                    <?php else: ?>
                        <a href="register.php" class="btn-nav-login" style="margin-right:6px;background:transparent;border:1px solid #8847ff;color:#8847ff">Register</a>
                        <a href="login.php" class="btn-nav-login">Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
