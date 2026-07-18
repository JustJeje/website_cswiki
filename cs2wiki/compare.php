<?php
session_start();
$current_page = "compare";

$knives_compare = [
    "karambit" => [
        "name"         => "Karambit",
        "image"        => "images/karambit.png",
        "color"        => "#e4432d",
        "avg_price"    => 800,
        "rarity_score" => 95,
        "cool_factor"  => 98,
        "anim_score"   => 97,
        "value_score"  => 70,
        "drop_rate"    => 72,
        "avg_label"    => "~$800 avg",
        "tier"         => "S+",
    ],
    "butterfly" => [
        "name"         => "Butterfly Knife",
        "image"        => "images/butterfly.png",
        "color"        => "#8847ff",
        "avg_price"    => 900,
        "rarity_score" => 93,
        "cool_factor"  => 97,
        "anim_score"   => 99,
        "value_score"  => 65,
        "drop_rate"    => 72,
        "avg_label"    => "~$900 avg",
        "tier"         => "S+",
    ],
    "m9bayonet" => [
        "name"         => "M9 Bayonet",
        "image"        => "images/m9bayonet.png",
        "color"        => "#4b96e5",
        "avg_price"    => 400,
        "rarity_score" => 88,
        "cool_factor"  => 85,
        "anim_score"   => 82,
        "value_score"  => 90,
        "drop_rate"    => 72,
        "avg_label"    => "~$400 avg",
        "tier"         => "A",
    ],
];

$stats_labels = [
    "rarity_score" => "Rarity Score",
    "cool_factor"  => "Cool Factor",
    "anim_score"   => "Animation",
    "value_score"  => "Value for Money",
    "drop_rate"    => "Drop Rate",
];

// FUNCTION: determine winner for a stat
function getWinner(array $knives, string $stat): string {
    $best_val = -1; $best_name = "";
    foreach ($knives as $k) {
        if ($k[$stat] > $best_val) { $best_val = $k[$stat]; $best_name = $k['name']; }
    }
    return $best_name;
}

// FUNCTION: get tier badge color — BRANCHING CASE 1
function getTierColor(string $tier): string {
    if ($tier === "S+") return "#fbbf24";
    elseif ($tier === "S") return "#e4432d";
    elseif ($tier === "A") return "#4b96e5";
    else return "#9ca3af";
}

// FUNCTION: get bar color per knife — BRANCHING CASE 2
function getBarColor(string $id): string {
    $colors = ["karambit" => "#e4432d", "butterfly" => "#8847ff", "m9bayonet" => "#4b96e5"];
    return $colors[$id] ?? "#888";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Knives — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header py-5 text-center">
    <div class="container">
        <h1 class="hero-title">KNIFE <span class="accent">COMPARE</span></h1>
        <p class="hero-sub">Side-by-side knife statistics</p>
    </div>
</div>

<section class="py-5">
    <div class="container">

        <!-- ===== KNIFE HEADER CARDS (dengan gambar besar) ===== -->
        <div class="row g-4 mb-5">
            <?php
            // LOOPING CASE 1: foreach render knife compare cards
            foreach ($knives_compare as $id => $k):
                $tier_color = getTierColor($k['tier']);
            ?>
            <div class="col-md-4">
                <div class="cmp-header-card" style="--knife-color: <?= $k['color'] ?>">
                    <!-- Colored top accent line -->
                    <div class="cmp-card-accent" style="background: <?= $k['color'] ?>"></div>

                    <!-- Knife image — centered, large -->
                    <div class="cmp-img-box">
                        <img
                            src="<?= htmlspecialchars($k['image']) ?>"
                            alt="<?= htmlspecialchars($k['name']) ?>"
                            class="cmp-knife-img"
                            onerror="this.parentElement.innerHTML='<div class=\'cmp-img-fallback\' style=\'color:<?= $k['color'] ?>\'>🗡️</div>'"
                        >
                    </div>

                    <!-- Knife info -->
                    <h4 class="cmp-knife-name" style="color: <?= $k['color'] ?>">
                        <?= htmlspecialchars($k['name']) ?>
                    </h4>
                    <div class="cmp-avg-price"><?= $k['avg_label'] ?></div>
                    <span class="cmp-tier-badge" style="color: <?= $tier_color ?>; border-color: <?= $tier_color ?>">
                        <?= $k['tier'] ?> Tier
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ===== STAT COMPARISON ROWS ===== -->
        <h2 class="section-title mb-4">📊 STAT <span class="accent">BREAKDOWN</span></h2>

        <?php
        // LOOPING CASE 2: for loop over each stat category
        $stat_keys = array_keys($stats_labels);
        for ($si = 0; $si < count($stat_keys); $si++):
            $stat   = $stat_keys[$si];
            $label  = $stats_labels[$stat];
            $winner = getWinner($knives_compare, $stat);
        ?>
        <div class="cmp-stat-block mb-3">
            <!-- Stat label row -->
            <div class="cmp-stat-header">
                <span class="cmp-stat-name"><?= htmlspecialchars($label) ?></span>
                <span class="cmp-stat-winner">🏆 <?= htmlspecialchars($winner) ?></span>
            </div>

            <!-- 3 bars side by side -->
            <div class="row g-3 mt-1">
                <?php foreach ($knives_compare as $id => $k): ?>
                <div class="col-md-4">
                    <div class="cmp-bar-group">
                        <div class="cmp-bar-label-row">
                            <span class="cmp-bar-knife-name"><?= htmlspecialchars($k['name']) ?></span>
                            <span class="cmp-bar-score" style="color: <?= getBarColor($id) ?>">
                                <?= $k[$stat] ?>
                            </span>
                        </div>
                        <div class="cmp-bar-track">
                            <div class="cmp-bar-fill"
                                 style="width: <?= $k[$stat] ?>%; background: linear-gradient(90deg, <?= getBarColor($id) ?>99, <?= getBarColor($id) ?>)">
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endfor; ?>

    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
</body>
</html>
