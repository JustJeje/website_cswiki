<?php
session_start();
require_once 'db.php';
$current_page = "knives";

$slug = isset($_GET['id']) ? trim($_GET['id']) : '';

$db = getDB();

// FUNCTION: return warna per wear level — BRANCHING switch
function getWearColor(string $wear): string {
    switch ($wear) {
        case "Factory New":    return "#4ade80";
        case "Minimal Wear":   return "#86efac";
        case "Field-Tested":   return "#fbbf24";
        case "Well-Worn":      return "#fb923c";
        case "Battle-Scarred": return "#f87171";
        default:               return "#9ca3af";
    }
}

// FUNCTION: return tier investasi — BRANCHING if/elseif
function getInvestmentTier(string $rarity): string {
    if ($rarity === "Covert")     return "High — Strong market retention";
    elseif ($rarity === "Classified") return "Medium-High — Stable value";
    elseif ($rarity === "Restricted") return "Medium — Accessible entry point";
    else                          return "Low — Budget option";
}

// READ: ambil knife dari DB berdasarkan slug
$stmt = $db->prepare('
    SELECT k.*, c.name AS category_name
    FROM knives k
    LEFT JOIN categories c ON k.category_id = c.id
    WHERE k.slug = ?
');
$stmt->bind_param('s', $slug);
$stmt->execute();
$knife = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil skins untuk knife ini
$skins = [];
if ($knife) {
    $stmt2 = $db->prepare('SELECT * FROM skins WHERE knife_id = ? ORDER BY id ASC');
    $stmt2->bind_param('i', $knife['id']);
    $stmt2->execute();
    $skins = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}

$db->close();

// Wear levels static (sama untuk semua knife di CS2)
$wear_levels = ["Factory New", "Minimal Wear", "Field-Tested", "Well-Worn", "Battle-Scarred"];

// BRANCHING: validasi knife ditemukan atau tidak
if (!$knife) {
    $error      = true;
    $page_title = "Knife Not Found";
} else {
    $error      = false;
    $page_title = $knife['name'] . " — CS2 Knife Wiki";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<?php if ($error): ?>
<div class="container py-5 text-center">
    <h1 class="hero-title">404</h1>
    <p class="hero-sub">Knife not found in the database.</p>
    <a href="knives.php" class="btn-cs2">← Back to All Knives</a>
</div>

<?php else: ?>
<div class="page-header py-4 text-center" style="--rarity-color: <?= htmlspecialchars($knife['rarity_color']) ?>">
    <div class="container">
        <div class="knife-img-wrap hero-knife-img mx-auto">
            <img
                src="<?= htmlspecialchars($knife['image']) ?>"
                alt="<?= htmlspecialchars($knife['name']) ?>"
                class="knife-img"
                onerror="this.src='images/placeholder.png'"
            >
        </div>
        <h1 class="hero-title mt-3" style="color: <?= htmlspecialchars($knife['rarity_color']) ?>">
            <?= htmlspecialchars($knife['name']) ?>
        </h1>
        <p class="hero-sub">
            <?= htmlspecialchars($knife['category_name'] ?? '—') ?> · Origin: <?= htmlspecialchars($knife['origin'] ?? '—') ?>
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">

                <!-- OVERVIEW -->
                <div class="detail-card">
                    <h2 class="section-title">Overview</h2>
                    <p class="detail-text">
                        <?= htmlspecialchars($knife['description'] ?? $knife['short_desc'] ?? '—') ?>
                    </p>
                    <?php if (!empty($knife['fun_fact'])): ?>
                    <div class="fun-fact-box mt-3">
                        <span class="accent">💡 Fun Fact:</span>
                        <?= htmlspecialchars($knife['fun_fact']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- WEAR LEVELS -->
                <div class="detail-card mt-4">
                    <h2 class="section-title">Wear Levels</h2>
                    <div class="wear-grid">
                        <?php
                        // LOOPING: foreach wear levels
                        foreach ($wear_levels as $wear):
                            $wcolor = getWearColor($wear);
                        ?>
                        <div class="wear-pill" style="border-color: <?= $wcolor ?>; color: <?= $wcolor ?>">
                            <?= htmlspecialchars($wear) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- TOP SKINS dari database -->
                <?php if (!empty($skins)): ?>
                <div class="detail-card mt-4">
                    <h2 class="section-title">Top Skins</h2>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php
                        // LOOPING: for loop dengan index nomor urut
                        for ($s = 0; $s < count($skins); $s++):
                        ?>
                        <span class="skin-pill large">
                            #<?= $s + 1 ?> <?= htmlspecialchars($skins[$s]['name']) ?>
                            <small style="color:#888;font-size:0.75em"> — <?= htmlspecialchars($skins[$s]['wear']) ?></small>
                        </span>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="col-lg-5">
                <!-- STATS -->
                <div class="detail-card" style="--rarity-color: <?= htmlspecialchars($knife['rarity_color']) ?>">
                    <h2 class="section-title">Knife Stats</h2>
                    <table class="stats-table w-100">
                        <tr>
                            <td>Rarity</td>
                            <td style="color:<?= htmlspecialchars($knife['rarity_color']) ?>">
                                <strong><?= htmlspecialchars($knife['rarity']) ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Market Price</td>
                            <td><strong><?= htmlspecialchars($knife['price_range'] ?? '—') ?></strong></td>
                        </tr>
                        <tr>
                            <td>Inspect Anim</td>
                            <td><?= htmlspecialchars($knife['inspect_anim'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td>Drop Chance</td>
                            <td><?= htmlspecialchars($knife['drop_chance'] ?? '~0.26%') ?></td>
                        </tr>
                        <tr>
                            <td>Investment</td>
                            <td><?= htmlspecialchars(getInvestmentTier($knife['rarity'])) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="detail-card mt-4 text-center">
                    <a href="knives.php" class="btn-cs2-outline w-100">← All Knives</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
</body>
</html>
