<?php
session_start();
$current_page = "about";

// FUNCTION: get tech badge color
function getTechColor(string $tech): string {
    $colors = [
        "PHP 8+"        => "#8847ff",
        "Bootstrap 5.3" => "#4b96e5",
        "HTML5"         => "#e4432d",
        "CSS3"          => "#4b96e5",
        "JavaScript"    => "#fbbf24",
    ];
    return $colors[$tech] ?? "#888";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-header py-5 text-center">
    <div class="container">
        <h1 class="hero-title">ABOUT <span class="accent">US</span></h1>
        <p class="hero-sub">Tentang aplikasi CS2 Knife Wiki</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row align-items-center g-4">
                    <div class="col-md-3 text-center">
                        <img src="images/logo.png" alt="Logo" style="max-width:140px;opacity:0.9;">
                    </div>
                    <div class="col-md-9">
                        <div class="detail-card">
                            <h2 style="font-family:'Orbitron',monospace;color:#fff">CS Knife Encyclopedia</h2>
                            <p class="detail-text">
                                A PHP-powered wiki dedicated to Counter-Strike knife skins.
                                This site covers the three most iconic knife types in CS2: the Karambit, Butterfly Knife, and M9 Bayonet — including their origins, stats, popular skins, and fun facts.
                            </p>
                            <p class="detail-text">
                                Built by <strong style="color:#fff">CS Knife Wiki Team</strong> as a PHP learning project demonstrating
                                variables, branching, looping, arrays, functions, procedures, sessions, cookies, and form handling.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <?php
                                $techs = ["PHP 8+", "Bootstrap 5.3", "HTML5", "CSS3", "JavaScript"];
                                // LOOPING: foreach tech stack badges
                                foreach ($techs as $tech):
                                    $tc = getTechColor($tech);
                                ?>
                                <span class="wear-pill" style="border-color:<?= $tc ?>;color:<?= $tc ?>">
                                    <?= htmlspecialchars($tech) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
</body>
</html>
