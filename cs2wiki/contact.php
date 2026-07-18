<?php
session_start();
$current_page = "contact";

// ==========================================
// ARRAY: contact form topics
// ==========================================
$topics = [
    "general"    => "General Question",
    "knife_info" => "Knife Information",
    "pricing"    => "Pricing / Market",
    "bug"        => "Report a Bug",
    "other"      => "Other",
];

$form_submitted = false;
$errors         = [];
$form_data      = [];

// ==========================================
// FUNCTION: sanitize input
// ==========================================
function sanitize(string $input): string {
    return htmlspecialchars(trim($input));
}

// FUNCTION: validate email
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// PROCEDURE: log submission to session history
function logSubmission(string $name, string $topic): void {
    if (!isset($_SESSION['contact_log'])) {
        $_SESSION['contact_log'] = [];
    }
    $_SESSION['contact_log'][] = [
        "name"  => $name,
        "topic" => $topic,
        "time"  => date("H:i:s"),
    ];
}

// ==========================================
// POST REQUEST: process form
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name']    ?? '');
    $email   = sanitize($_POST['email']   ?? '');
    $topic   = sanitize($_POST['topic']   ?? '');
    $message = sanitize($_POST['message'] ?? '');

    $form_data = compact('name', 'email', 'topic', 'message');

    // BRANCHING CASE 1: validate name
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters.";
    }

    // BRANCHING CASE 2: validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate topic
    if (empty($topic) || !array_key_exists($topic, $topics)) {
        $errors[] = "Please select a valid topic.";
    }

    // Validate message
    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters.";
    }

    // If no errors, process
    if (empty($errors)) {
        logSubmission($name, $topic);
        $form_submitted = true;
        $form_data      = []; // clear form
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-header py-5 text-center">
    <div class="container">
        <h1 class="hero-title">CONTACT <span class="accent">US</span></h1>
        <p class="hero-sub">Have questions? Spotted an error? Reach out below.</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <?php if ($form_submitted): ?>
                <!-- SUCCESS STATE -->
                <div class="login-card text-center">
                    <div style="font-size:3rem">✅</div>
                    <h3 class="mt-3 accent" style="font-family: 'Orbitron', sans-serif;">Message Sent!</h3>
                    <p style="color:#aaa">Thanks for reaching out. We'll get back to you soon, Agent.</p>
                    <a href="index.php" class="btn-cs2 mt-3">Return Home</a>

                    <?php if (isset($_SESSION['contact_log']) && count($_SESSION['contact_log']) > 0): ?>
                    <div class="mt-4 text-start" style="background:rgba(255,255,255,0.03); padding:1rem; border-radius:8px; border:1px solid #333">
                        <p style="color:#888; font-size:0.85rem; margin-bottom:0.5rem">Session — Previous submissions this session:</p>
                        <?php
                        // LOOPING: show submission history from session
                        foreach ($_SESSION['contact_log'] as $log):
                        ?>
                        <div style="color:#ccc; font-size:0.8rem">
                            📨 <?= sanitize($log['name']) ?> — <?= $topics[$log['topic']] ?? $log['topic'] ?> at <?= $log['time'] ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php else: ?>
                <!-- CONTACT FORM -->
                <div class="login-card">
                    <h3 style="font-family:'Rajdhani',sans-serif; color:#fff; margin-bottom:1.5rem;">Send a Message</h3>

                    <?php if (!empty($errors)): ?>
                    <div class="alert-cs2 error mb-3">
                        <?php foreach ($errors as $err): ?>
                        <div>⚠ <?= htmlspecialchars($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php">

                        <div class="row g-3">
                            <!-- INPUT 1: Name -->
                            <div class="col-md-6">
                                <label class="form-label-cs2">Your Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-input-cs2"
                                    placeholder="Agent name"
                                    value="<?= htmlspecialchars($form_data['name'] ?? '') ?>"
                                    required
                                >
                            </div>

                            <!-- INPUT 2: Email -->
                            <div class="col-md-6">
                                <label class="form-label-cs2">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-input-cs2"
                                    placeholder="your@email.com"
                                    value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                    required
                                >
                            </div>
                        </div>

                        <!-- INPUT 3: Topic dropdown -->
                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Topic</label>
                            <select name="topic" class="form-input-cs2" required>
                                <option value="">— Select a topic —</option>
                                <?php foreach ($topics as $val => $label): ?>
                                <option value="<?= $val ?>" <?= (($form_data['topic'] ?? '') === $val) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- INPUT 4: Message -->
                        <div class="form-group-cs2 mt-3">
                            <label class="form-label-cs2">Message</label>
                            <textarea
                                name="message"
                                class="form-input-cs2"
                                rows="5"
                                placeholder="Write your message here..."
                                required
                            ><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn-cs2 w-100 mt-4">SEND MESSAGE →</button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
