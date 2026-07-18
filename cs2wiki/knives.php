<?php
session_start();
require_once 'db.php';
$current_page = "knives";

$db = getDB();

// FUNCTION: return bintang sesuai rarity
function rarityStars(string $rarity): string {
    if ($rarity === "Covert")     return "★★★★★";
    elseif ($rarity === "Classified") return "★★★★☆";
    elseif ($rarity === "Restricted") return "★★★☆☆";
    else                          return "★★☆☆☆";
}

// READ: ambil semua knife dari database
$knives = $db->query('
    SELECT k.*, c.name AS category_name
    FROM knives k
    LEFT JOIN categories c ON k.category_id = c.id
    ORDER BY k.id ASC
')->fetch_all(MYSQLI_ASSOC);

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Knives — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-header py-5 text-center">
    <div class="container">
        <h1 class="hero-title">ALL <span class="accent">KNIVES</span></h1>
        <p class="hero-sub">Browse the complete CS2 Knife collection</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php
            // LOOPING: foreach tampilkan semua knife dari database
            foreach ($knives as $knife):
            ?>
            <div class="col-md-4">
                <div class="knife-card" style="--rarity-color: <?= htmlspecialchars($knife['rarity_color']) ?>">

                    <div class="knife-img-wrap">
                        <img
                            src="<?= htmlspecialchars($knife['image']) ?>"
                            alt="<?= htmlspecialchars($knife['name']) ?>"
                            class="knife-img"
                            onerror="this.src='images/placeholder.png'"
                        >
                    </div>

                    <span class="rarity-badge" style="color: <?= htmlspecialchars($knife['rarity_color']) ?>">
                        <?= rarityStars($knife['rarity']) ?> <?= htmlspecialchars($knife['rarity']) ?>
                    </span>
                    <h3 class="knife-name"><?= htmlspecialchars($knife['name']) ?></h3>
                    <p class="knife-type">Type: <?= htmlspecialchars($knife['category_name'] ?? '—') ?></p>
                    <p class="knife-desc"><?= htmlspecialchars($knife['short_desc'] ?? '') ?></p>

                    <div class="price-row">
                        Market Price: <span class="price-tag"><?= htmlspecialchars($knife['price_range'] ?? '—') ?></span>
                    </div>

                    <a href="detail.php?id=<?= htmlspecialchars($knife['slug']) ?>" class="btn-cs2-sm mt-3 d-block text-center">View Full Details →</a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($knives)): ?>
            <div class="col-12 text-center py-5">
                <p style="color:#555">No knives in database yet.</p>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="btn-cs2">➕ Add Knife</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
</body>
</html>
