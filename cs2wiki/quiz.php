<?php
session_start();
$current_page = "quiz";

// ARRAY: Quiz questions
$questions = [
    [
        "q"       => "Knife manakah yang punya animasi inspect paling kompleks di CS2?",
        "options" => ["Karambit", "Butterfly Knife", "M9 Bayonet", "Huntsman"],
        "answer"  => 1,
        "reason"  => "Butterfly Knife punya animasi flipping balisong yang paling detail dan membutuhkan waktu pengembangan terlama.",
        "img"     => "images/butterfly.png",
    ],
    [
        "q"       => "Dari mana asal usul pisau Karambit?",
        "options" => ["Jepang", "Filipina", "Asia Tenggara (Indonesia/Malaysia)", "Amerika"],
        "answer"  => 2,
        "reason"  => "Karambit berasal dari Asia Tenggara, terinspirasi dari cakar harimau dalam seni bela diri Silat.",
        "img"     => "images/karambit.png",
    ],
    [
        "q"       => "Berapa perkiraan harga Butterfly Knife Factory New di market CS2?",
        "options" => ["\$50–\$100", "\$100–\$200", "\$300–\$5,000+", "\$10–\$50"],
        "answer"  => 2,
        "reason"  => "Butterfly Knife Factory New bisa dihargai \$300 hingga \$5,000+ tergantung skin-nya.",
        "img"     => "images/butterfly.png",
    ],
    [
        "q"       => "M9 Bayonet terinspirasi dari senjata militer negara mana?",
        "options" => ["Rusia", "Amerika Serikat", "Jerman", "Inggris"],
        "answer"  => 1,
        "reason"  => "M9 Bayonet adalah bayonet standar militer Amerika Serikat (US Army) sejak 1980-an.",
        "img"     => "images/m9bayonet.png",
    ],
    [
        "q"       => "Skin apa yang paling populer untuk Karambit di CS2?",
        "options" => ["Crimson Web", "Doppler", "Safari Mesh", "Forest DDPAT"],
        "answer"  => 1,
        "reason"  => "Karambit Doppler adalah salah satu skin paling ikonik dan paling banyak dicari.",
        "img"     => "images/karambit.png",
    ],
];

// PROCEDURE: save quiz result to session
function saveQuizResult(int $score, int $total): void {
    $_SESSION['last_quiz_score'] = $score;
    $_SESSION['last_quiz_total'] = $total;
    $_SESSION['last_quiz_time']  = date("H:i:s");
}

// FUNCTION: get result message — BRANCHING CASE 1
function getResultMessage(int $score, int $total): string {
    $pct = ($score / $total) * 100;
    if ($pct === 100.0)    return "🏆 PERFECT! Kamu adalah Master CS2 Knife!";
    elseif ($pct >= 80)    return "🔥 Luar biasa! Pengetahuan knife lo sangat tinggi!";
    elseif ($pct >= 60)    return "👍 Bagus! Lo tau cukup banyak soal CS2 knife.";
    elseif ($pct >= 40)    return "😅 Lumayan, tapi masih perlu banyak belajar nih.";
    else                   return "💀 Belajar dulu yuk dari halaman Knives!";
}

// FUNCTION: get result color — BRANCHING CASE 2
function getResultColor(int $score, int $total): string {
    $pct = ($score / $total) * 100;
    if ($pct >= 80)      return "#4ade80";
    elseif ($pct >= 60)  return "#fbbf24";
    else                 return "#f87171";
}

$submitted    = false;
$score        = 0;
$user_answers = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOOPING: for loop to check each answer
    for ($i = 0; $i < count($questions); $i++) {
        $user_answers[$i] = isset($_POST["q$i"]) ? (int)$_POST["q$i"] : -1;
        if ($user_answers[$i] === $questions[$i]['answer']) $score++;
    }
    saveQuizResult($score, count($questions));
    $submitted = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz — CS2 Knife Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Orbitron:wght@700;900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- PAGE HEADER -->
<div class="page-header py-5 text-center">
    <div class="container">
        <h1 class="hero-title">KNIFE <span class="accent">QUIZ</span></h1>
        <p class="hero-sub">Seberapa dalam pengetahuan CS2 knife kamu?</p>
        <?php if (isset($_SESSION['last_quiz_score'])): ?>
        <div class="session-badge mt-3">
            Skor terakhir: <strong><?= $_SESSION['last_quiz_score'] ?>/<?= $_SESSION['last_quiz_total'] ?></strong>
            &nbsp;(<?= $_SESSION['last_quiz_time'] ?>)
        </div>
        <?php endif; ?>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">

            <?php if ($submitted): ?>
            <!-- ========== RESULT PAGE ========== -->
            <div class="quiz-result-card text-center">

                <!-- Score circle -->
                <div class="quiz-score-circle" style="--result-color: <?= getResultColor($score, count($questions)) ?>">
                    <span class="quiz-score-num" style="color: <?= getResultColor($score, count($questions)) ?>">
                        <?= $score ?>/<?= count($questions) ?>
                    </span>
                </div>
                <p class="quiz-result-msg mt-3"><?= getResultMessage($score, count($questions)) ?></p>

                <!-- Review each Q -->
                <div class="quiz-review-list mt-4">
                <?php
                // LOOPING: foreach show each Q&A review
                foreach ($questions as $qi => $q):
                    $is_correct = ($user_answers[$qi] === $q['answer']);
                ?>
                <div class="quiz-review-item <?= $is_correct ? 'correct' : 'wrong' ?>">
                    <div class="quiz-review-left">
                        <img src="<?= htmlspecialchars($q['img']) ?>" alt="knife" class="quiz-review-img"
                             onerror="this.style.display='none'">
                    </div>
                    <div class="quiz-review-right">
                        <p class="quiz-review-q">
                            <span class="quiz-review-num">Q<?= $qi+1 ?></span>
                            <?= htmlspecialchars($q['q']) ?>
                        </p>
                        <p class="quiz-review-ans">
                            <?= $is_correct ? '✅' : '❌' ?>
                            Jawaban benar: <strong><?= htmlspecialchars($q['options'][$q['answer']]) ?></strong>
                        </p>
                        <?php if (!$is_correct): ?>
                        <p class="quiz-review-reason"><?= htmlspecialchars($q['reason']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>

                <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
                    <a href="quiz.php" class="btn-cs2">🔄 Coba Lagi</a>
                    <a href="knives.php" class="btn-cs2-outline">📖 Pelajari Knife</a>
                </div>
            </div>

            <?php else: ?>
            <!-- ========== QUIZ FORM ========== -->

            <!-- Progress bar -->
            <div class="quiz-progress-wrap mb-4">
                <div class="quiz-progress-label">
                    <span><?= count($questions) ?> Pertanyaan</span>
                    <span class="accent">CS2 Knife Quiz</span>
                </div>
                <div class="quiz-progress-bar">
                    <div class="quiz-progress-fill" style="width: 0%"></div>
                </div>
            </div>

            <form method="POST" action="quiz.php" id="quizForm">
                <?php
                // LOOPING: for loop render each question card
                for ($qi = 0; $qi < count($questions); $qi++):
                    $q = $questions[$qi];
                ?>
                <div class="quiz-question-card">

                    <!-- Question header: number + image -->
                    <div class="quiz-card-header">
                        <div class="quiz-card-meta">
                            <span class="quiz-q-num">Q<?= $qi+1 ?></span>
                            <span class="quiz-q-of">/ <?= count($questions) ?></span>
                        </div>
                        <img src="<?= htmlspecialchars($q['img']) ?>"
                             alt="knife hint"
                             class="quiz-card-img"
                             onerror="this.style.display='none'">
                    </div>

                    <!-- Question text -->
                    <p class="quiz-q-text"><?= htmlspecialchars($q['q']) ?></p>

                    <!-- Options grid 2x2 -->
                    <div class="quiz-options-grid">
                        <?php for ($oi = 0; $oi < count($q['options']); $oi++): ?>
                        <label class="quiz-option-tile">
                            <input type="radio" name="q<?= $qi ?>" value="<?= $oi ?>" required>
                            <span class="quiz-option-letter"><?= chr(65 + $oi) ?></span>
                            <span class="quiz-option-text"><?= htmlspecialchars($q['options'][$oi]) ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>

                </div>
                <?php endfor; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn-cs2 px-5 py-3" style="font-size:1rem">
                        ✅ Submit Jawaban
                    </button>
                </div>
            </form>
            <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>
<script>
// Animate progress bar as user answers
const radios = document.querySelectorAll('input[type="radio"]');
const fill   = document.querySelector('.quiz-progress-fill');
const total  = <?= count($questions) ?>;
let answered = new Set();

if (fill) {
    radios.forEach(r => {
        r.addEventListener('change', () => {
            answered.add(r.name);
            fill.style.width = (answered.size / total * 100) + '%';
        });
    });
}

// Highlight selected option tile
radios.forEach(r => {
    r.addEventListener('change', () => {
        const group = document.querySelectorAll(`input[name="${r.name}"]`);
        group.forEach(g => g.closest('.quiz-option-tile').classList.remove('selected'));
        r.closest('.quiz-option-tile').classList.add('selected');
    });
});
</script>
</body>
</html>
