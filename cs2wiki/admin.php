<?php
// ==========================================
// admin.php — Admin Panel: CRUD Knives
// CS2 Knife Wiki | UAS PHP
// ==========================================
session_start();
require_once 'db.php';

$current_page = 'admin';

// BRANCHING: cek login & role admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db      = getDB();
$message = '';
$error   = '';

// ==========================================
// FUNCTION: handle upload gambar — $_FILES
// ==========================================
function uploadImage(): string {
    // Kalau ga ada file yang di-upload, return path default atau path lama
    if (empty($_FILES['image_file']['name'])) {
        return trim($_POST['image'] ?? 'images/placeholder.png');
    }

    $file     = $_FILES['image_file'];
    $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // BRANCHING: validasi tipe file
    if (!in_array($file['type'], $allowed)) {
        return 'ERROR:Format file harus JPG, PNG, GIF, atau WEBP.';
    }

    // BRANCHING: validasi ukuran file
    if ($file['size'] > $max_size) {
        return 'ERROR:Ukuran file maksimal 2MB.';
    }

    // Buat nama file unik biar ga bentrok
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'knife_' . time() . '_' . rand(100, 999) . '.' . $ext;
    
    $target = __DIR__ . '/images/' . $filename;

    // BRANCHING: pindahkan file dari temp ke folder images/
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'images/' . $filename; 
    }

    return 'ERROR:Upload gagal. Cek permission folder images/.';
}

// ==========================================
// CREATE — tambah knife baru
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $slug         = trim($_POST['slug']         ?? '');
    $name         = trim($_POST['name']         ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $origin      = trim($_POST['origin']       ?? '');
    $rarity      = trim($_POST['rarity']       ?? 'Covert');
    $rarity_color= trim($_POST['rarity_color'] ?? '#e4432d');
    $price_range = trim($_POST['price_range']  ?? '');
    $short_desc  = trim($_POST['short_desc']   ?? '');

    // Upload gambar
    $image = uploadImage();

    if (substr($image, 0, 6) === 'ERROR:') {
        $error = '❌ ' . substr($image, 6);
    } elseif (empty($slug) || empty($name)) {
        $error = 'Slug and Name are required.';
    } else {
        $stmt = $db->prepare('INSERT INTO knives (slug, name, category_id, origin, rarity, rarity_color, price_range, short_desc, image) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssissssss', $slug, $name, $category_id, $origin, $rarity, $rarity_color, $price_range, $short_desc, $image);
        if ($stmt->execute()) {
            $message = '✅ Knife "' . htmlspecialchars($name) . '" added successfully!';
        } else {
            $error = '❌ Failed to add knife. Slug might already exist.';
        }
        $stmt->close();
    }
}

// ==========================================
// UPDATE — edit knife
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id          = (int)($_POST['id']          ?? 0);
    $name        = trim($_POST['name']         ?? '');
    $origin      = trim($_POST['origin']       ?? '');
    $rarity      = trim($_POST['rarity']       ?? 'Covert');
    $rarity_color= trim($_POST['rarity_color'] ?? '#e4432d');
    $price_range = trim($_POST['price_range']  ?? '');
    $short_desc  = trim($_POST['short_desc']   ?? '');

    // Upload gambar (kalau ga upload baru, pakai path lama)
    $image = uploadImage();

    if (substr($image, 0, 6) === 'ERROR:') {
        $error = '❌ ' . substr($image, 6);
    } else {
        $stmt = $db->prepare('UPDATE knives SET name=?, origin=?, rarity=?, rarity_color=?, price_range=?, short_desc=?, image=? WHERE id=?');
        $stmt->bind_param('sssssssi', $name, $origin, $rarity, $rarity_color, $price_range, $short_desc, $image, $id);
        if ($stmt->execute()) {
            $message = '✅ Knife updated successfully!';
        } else {
            $error = '❌ Update failed.';
        }
        $stmt->close();
    }
}

// ==========================================
// DELETE — hapus knife
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM knives WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $message = '✅ Knife deleted.';
    } else {
        $error = '❌ Delete failed.';
    }
    $stmt->close();
}

// ==========================================
// SEARCH + READ — ambil data dari DB
// ==========================================
$search  = isset($_GET['search']) ? trim($_GET['search']) : '';
$edit_id = isset($_GET['edit'])   ? (int)$_GET['edit']   : 0;

if (!empty($search)) {
    $like  = '%' . $search . '%';
    $stmt  = $db->prepare('SELECT k.*, c.name AS category_name FROM knives k LEFT JOIN categories c ON k.category_id = c.id WHERE k.name LIKE ? OR k.origin LIKE ? OR k.rarity LIKE ? ORDER BY k.id ASC');
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $knives_result = $stmt->get_result();
    $stmt->close();
} else {
    $knives_result = $db->query('SELECT k.*, c.name AS category_name FROM knives k LEFT JOIN categories c ON k.category_id = c.id ORDER BY k.id ASC');
}

$knives     = $knives_result->fetch_all(MYSQLI_ASSOC);
$categories = $db->query('SELECT * FROM categories ORDER BY name ASC')->fetch_all(MYSQLI_ASSOC);

$edit_knife = null;
if ($edit_id > 0) {
    $stmt = $db->prepare('SELECT * FROM knives WHERE id = ?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_knife = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { background: #0d0d1a; border-radius: 12px; overflow: hidden; }
        .admin-table th { background: #1a1a2e; color: #8847ff; font-family: 'Rajdhani', sans-serif; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border: none; padding: 12px 16px; }
        .admin-table td { border-color: #1e1e3a; color: #ccc; font-size: 0.9rem; padding: 10px 16px; vertical-align: middle; }
        .admin-table tr:hover td { background: rgba(136,71,255,0.05); }
        .thumb { width: 50px; height: 50px; object-fit: contain; background: #0d0d1a; border-radius: 6px; }
        .form-admin { background: #0d0d1a; border: 1px solid #1e1e3a; border-radius: 12px; padding: 24px; }
        .badge-rarity { font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; background: rgba(255,255,255,0.05); }
        .upload-box { border: 2px dashed #2a2a4a; border-radius: 8px; padding: 12px; text-align: center; cursor: pointer; transition: border-color 0.2s; }
        .upload-box:hover { border-color: #8847ff; }
        .upload-box input[type=file] { display: none; }
        .preview-img { max-width: 100%; max-height: 80px; object-fit: contain; border-radius: 6px; margin-top: 8px; display: none; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-header py-4 text-center">
    <div class="container">
        <h1 class="hero-title" style="font-size:2.2rem">ADMIN <span class="accent">PANEL</span></h1>
        <p class="hero-sub">Manage Knife Database — CRUD Operations</p>
        <small style="color:#555">Logged in as: <strong style="color:#8847ff"><?= htmlspecialchars($_SESSION['user']) ?></strong></small>
    </div>
</div>

<div class="container pb-5">

    <?php if ($message): ?>
    <div class="alert-cs2 success mb-4"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert-cs2 error mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-4">
            <div class="form-admin">
                <?php if ($edit_knife): ?>
                    <h5 class="mb-3" style="color:#fbbf24;font-family:'Rajdhani',sans-serif">✏️ EDIT KNIFE</h5>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $edit_knife['id'] ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($edit_knife['image']) ?>">
                <?php else: ?>
                    <h5 class="mb-3" style="color:#4ade80;font-family:'Rajdhani',sans-serif">➕ ADD NEW KNIFE</h5>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="image" value="images/placeholder.png">
                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Slug <small style="color:#555">(URL key, no spaces)</small></label>
                            <input type="text" name="slug" class="form-input-cs2" placeholder="e.g. gut_knife" required>
                        </div>
                <?php endif; ?>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Knife Name</label>
                            <input type="text" name="name" class="form-input-cs2" placeholder="e.g. Gut Knife"
                                value="<?= $edit_knife ? htmlspecialchars($edit_knife['name']) : '' ?>" required>
                        </div>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Category</label>
                            <select name="category_id" class="form-input-cs2">
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ($edit_knife && $edit_knife['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Origin</label>
                            <input type="text" name="origin" class="form-input-cs2" placeholder="e.g. United States"
                                value="<?= $edit_knife ? htmlspecialchars($edit_knife['origin']) : '' ?>">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label-cs2">Rarity</label>
                                <select name="rarity" class="form-input-cs2">
                                    <?php foreach (['Covert','Classified','Restricted','Mil-Spec'] as $r): ?>
                                    <option value="<?= $r ?>" <?= ($edit_knife && $edit_knife['rarity'] === $r) ? 'selected' : '' ?>>
                                        <?= $r ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label-cs2">Color</label>
                                <input type="color" name="rarity_color" class="form-input-cs2" style="padding:4px;height:42px"
                                    value="<?= $edit_knife ? htmlspecialchars($edit_knife['rarity_color']) : '#e4432d' ?>">
                            </div>
                        </div>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Price Range</label>
                            <input type="text" name="price_range" class="form-input-cs2" placeholder="e.g. $50 — $800+"
                                value="<?= $edit_knife ? htmlspecialchars($edit_knife['price_range']) : '' ?>">
                        </div>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">Short Description</label>
                            <textarea name="short_desc" class="form-input-cs2" rows="2" style="resize:vertical"><?= $edit_knife ? htmlspecialchars($edit_knife['short_desc']) : '' ?></textarea>
                        </div>

                        <div class="form-group-cs2 mb-3">
                            <label class="form-label-cs2">
                                Knife Image
                                <?php if ($edit_knife && !empty($edit_knife['image'])): ?>
                                <small style="color:#555">(kosongkan = pakai foto lama)</small>
                                <?php endif; ?>
                            </label>

                            <?php if ($edit_knife && !empty($edit_knife['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($edit_knife['image']) ?>"
                                     style="max-height:60px;object-fit:contain;border-radius:6px;background:#111;padding:4px"
                                     onerror="this.style.display='none'">
                                <small style="color:#555;display:block;margin-top:4px">Foto saat ini</small>
                            </div>
                            <?php endif; ?>

                            <div class="upload-box" onclick="document.getElementById('img_upload').click()">
                                <span style="color:#666;font-size:0.85rem">📁 Klik untuk pilih foto</span><br>
                                <small style="color:#444">JPG, PNG, GIF, WEBP — maks 2MB</small>
                                <input type="file" id="img_upload" name="image_file"
                                       accept="image/jpeg,image/png,image/gif,image/webp"
                                       onchange="previewImage(this)">
                                <img id="img_preview" class="preview-img" alt="Preview">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <?php if ($edit_knife): ?>
                                <button type="submit" class="btn-cs2 flex-fill">UPDATE ✓</button>
                                <a href="admin.php" class="btn-cs2-outline flex-fill text-center" style="padding:10px">CANCEL</a>
                            <?php else: ?>
                                <button type="submit" class="btn-cs2 w-100">ADD KNIFE ➕</button>
                            <?php endif; ?>
                        </div>
                    </form>
            </div>
        </div>

        <div class="col-lg-8">
            <form method="GET" action="admin.php" class="d-flex gap-2 mb-4">
                <input type="text" name="search" class="form-input-cs2 flex-fill"
                    placeholder="🔍 Search knife by name, origin, or rarity..."
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn-cs2" style="white-space:nowrap">SEARCH</button>
                <?php if (!empty($search)): ?>
                <a href="admin.php" class="btn-cs2-outline" style="padding:10px 16px;white-space:nowrap">CLEAR</a>
                <?php endif; ?>
            </form>

            <?php if (!empty($search)): ?>
            <p style="color:#666;font-size:0.85rem" class="mb-2">
                Showing <strong style="color:#8847ff"><?= count($knives) ?></strong> result(s) for "<?= htmlspecialchars($search) ?>"
            </p>
            <?php endif; ?>

            <div class="table-responsive admin-table">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>IMG</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Rarity</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($knives)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#555">
                                No knives found <?= !empty($search) ? 'for "' . htmlspecialchars($search) . '"' : '' ?>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($knives as $k): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($k['image']) ?>" class="thumb"
                                     alt="<?= htmlspecialchars($k['name']) ?>"
                                     onerror="this.src='images/placeholder.png'">
                            </td>
                            <td>
                                <strong style="color:#eee"><?= htmlspecialchars($k['name']) ?></strong>
                                <br><small style="color:#555"><?= htmlspecialchars($k['slug']) ?></small>
                            </td>
                            <td style="color:#aaa"><?= htmlspecialchars($k['category_name'] ?? '—') ?></td>
                            <td>
                                <span class="badge-rarity" style="color:<?= htmlspecialchars($k['rarity_color']) ?>">
                                    <?= htmlspecialchars($k['rarity']) ?>
                                </span>
                            </td>
                            <td style="color:#fbbf24;font-size:0.8rem"><?= htmlspecialchars($k['price_range'] ?? '—') ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="admin.php?edit=<?= $k['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                                       class="btn btn-sm" style="background:#1e1e3a;color:#fbbf24;border:1px solid #fbbf24;font-size:0.75rem">
                                        Edit
                                    </a>
                                    <form method="POST" action="admin.php"
                                          onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($k['name'])) ?>?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                        <button type="submit" class="btn btn-sm"
                                                style="background:#1e1e3a;color:#f87171;border:1px solid #f87171;font-size:0.75rem">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="mt-2" style="color:#444;font-size:0.8rem">
                Total: <?= count($knives) ?> knife(s) in database
            </p>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input) {
    const preview = document.getElementById('img_preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>