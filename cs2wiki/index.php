<?php
session_start();

$site_name    = "CS2 Knife Wiki";
$site_tagline = "The Ultimate Counter-Strike 2 Knife Encyclopedia";
$current_page = "home";

// COOKIE: track visit count
if (isset($_COOKIE['visit_count'])) {
    $visit_count = (int)$_COOKIE['visit_count'] + 1;
} else {
    $visit_count = 1;
}
setcookie('visit_count', $visit_count, time() + (86400 * 30), "/");

// ARRAY of featured knives — with image paths instead of emoji
$featured_knives = [
    [
        "id"    => "karambit",
        "name"  => "Karambit",
        "rarity"=> "Covert",
        "color" => "#e4432d",
        "image" => "images/karambit.png",
        "desc"  => "Inspired by the tiger's claw, the Karambit is the most iconic knife in CS2.",
    ],
    [
        "id"    => "butterfly",
        "name"  => "Butterfly Knife",
        "rarity"=> "Covert",
        "color" => "#8847ff",
        "image" => "images/butterfly.png",
        "desc"  => "A balisong-style flipping knife known for its mesmerizing animations.",
    ],
    [
        "id"    => "m9bayonet",
        "name"  => "M9 Bayonet",
        "rarity"=> "Covert",
        "color" => "#4b96e5",
        "image" => "images/m9bayonet.png",
        "desc"  => "A military-grade combat knife with a clip-point blade and iconic design.",
    ],
];

function getGreeting() {
    $hour = (int)date("H");
    if ($hour >= 5 && $hour < 12)       return "Good Morning, Agent";
    elseif ($hour >= 12 && $hour < 17)  return "Good Afternoon, Agent";
    elseif ($hour >= 17 && $hour < 21)  return "Good Evening, Agent";
    else                                 return "Welcome Back, Night Owl";
}

$greeting = getGreeting();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_name ?> — Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container position-relative z-1 text-center">
        <p class="greeting-text"><?= htmlspecialchars($greeting) ?></p>
        <h1 class="hero-title">CS2 KNIFE<br><span class="accent">WIKI</span></h1>
        <p class="hero-sub"><?= $site_tagline ?></p>

        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
            <div class="session-badge">
                ✅ Logged in as <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
            </div>
        <?php else: ?>
            <a href="login.php" class="btn-cs2 me-2">Login</a>
        <?php endif; ?>
        <a href="knives.php" class="btn-cs2-outline">View All Knives</a>
    </div>
</section>

<!-- FEATURED KNIVES -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Featured <span class="accent">Knives</span></h2>
        <div class="row g-4 mt-2">
            <?php
            // LOOPING CASE 1: foreach
            foreach ($featured_knives as $knife):
            ?>
            <div class="col-md-4">
                <div class="knife-card" style="--rarity-color: <?= $knife['color'] ?>">
                    <!-- IMAGE instead of emoji -->
                    <div class="knife-img-wrap">
                        <img
                            src="<?= htmlspecialchars($knife['image']) ?>"
                            alt="<?= htmlspecialchars($knife['name']) ?>"
                            class="knife-img"
                            onerror="this.src='images/placeholder.png'"
                        >
                    </div>
                    <span class="rarity-badge" style="color: <?= $knife['color'] ?>">
                        ★ <?= htmlspecialchars($knife['rarity']) ?>
                    </span>
                    <h3 class="knife-name"><?= htmlspecialchars($knife['name']) ?></h3>
                    <p class="knife-desc"><?= htmlspecialchars($knife['desc']) ?></p>
                    <a href="detail.php?id=<?= $knife['id'] ?>" class="btn-cs2-sm">View Details →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<section class="stats-section py-4">
    <div class="container">
        <div class="row text-center g-3">
            <?php
            $stats = [
                ["label" => "Knives Documented", "value" => "3"],
                ["label" => "Your Visits",        "value" => $visit_count],
                ["label" => "Rarity Tier",        "value" => "Covert"],
                ["label" => "Wiki Version",        "value" => "2.0"],
            ];
            // LOOPING CASE 2: for loop
            for ($i = 0; $i < count($stats); $i++):
            ?>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-value"><?= $stats[$i]['value'] ?></div>
                    <div class="stat-label"><?= $stats[$i]['label'] ?></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
</body>
</html>
