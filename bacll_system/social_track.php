<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$errors = [];
$saved  = null;

$limits = [
    'khmer_lit'     => 125,
    'history'       => 75,
    'geography'     => 75,
    'moral_civic'   => 75,
    'math'          => 75,
    'foreign_lang'  => 50,
    'earth_science' => 50,
];
$totalMax = array_sum($limits); // 525

$labels = [
    'khmer_lit'     => 'Khmer Literature',
    'history'       => 'History',
    'geography'     => 'Geography',
    'moral_civic'   => 'Moral-Civic Education',
    'math'          => 'Mathematics',
    'foreign_lang'  => 'Foreign Language (English)',
    'earth_science' => 'Earth Science (Earth and Ecology)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid session.";
    }

    $student_id   = trim($_POST['student_id'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');

    if ($student_id === '')   $errors[] = "Student ID is required.";
    if ($student_name === '') $errors[] = "Student name is required.";

    $scores = [];
    foreach ($limits as $subject => $max) {
        $val = isset($_POST[$subject]) ? (float) $_POST[$subject] : -1;
        if ($val < 0 || $val > $max) {
            $errors[] = $labels[$subject] . " must be between 0 and $max.";
        } else {
            $scores[$subject] = $val;
        }
    }

    if (empty($errors)) {
        $total   = array_sum($scores);
        $average = $total / $totalMax * 100;

        if ($average >= 90)      $grade = 'A';
        elseif ($average >= 80)  $grade = 'B';
        elseif ($average >= 70)  $grade = 'C';
        elseif ($average >= 60)  $grade = 'D';
        elseif ($average >= 50)  $grade = 'E';
        else                     $grade = 'F';

        $stmt = $conn->prepare(
            "INSERT INTO students
             (student_id, student_name, track,
              khmer_lit, history, geography, moral_civic, math, foreign_lang, earth_science,
              total, average, grade)
             VALUES (?, ?, 'social', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssddddddddds",
            $student_id, $student_name,
            $scores['khmer_lit'], $scores['history'], $scores['geography'],
            $scores['moral_civic'], $scores['math'], $scores['foreign_lang'],
            $scores['earth_science'], $total, $average, $grade
        );

        if ($stmt->execute()) {
            $saved = [
                'name'    => $student_name,
                'id'      => $student_id,
                'total'   => $total,
                'average' => $average,
                'grade'   => $grade,
            ];
        } else {
            $errors[] = "DB error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="dashboard-wrapper">
    <div class="container">
        <div class="topbar">
            <h2>Social Class Track</h2>
            <a href="student_dashboard.php" class="btn btn-secondary">Back</a>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success">
                Student <strong><?= htmlspecialchars($saved['name']) ?></strong>
                (<?= htmlspecialchars($saved['id']) ?>) saved.
                Total: <?= $saved['total'] ?> / <?= $totalMax ?> -
                Average: <?= number_format($saved['average'], 2) ?>% -
                Grade: <strong><?= $saved['grade'] ?></strong>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <p style="color:#6b7280; font-size:14px; margin-bottom:16px; text-align:center;">
            You selected: <strong>Social Class</strong> - Total possible score: <strong><?= $totalMax ?></strong>
        </p>

        <div class="table-wrapper">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="grade-grid two-col">
                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="student_id" class="form-control"
                               placeholder="e.g. STU-001" required>
                    </div>
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" class="form-control"
                               placeholder="e.g. Sok Dara" required>
                    </div>
                </div>

                <div class="grade-grid" style="grid-template-columns: 1fr; padding-top: 0;">
                    <?php foreach ($limits as $subject => $max): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($labels[$subject]) ?> (0-<?= $max ?>)</label>
                            <input type="number" step="0.01" min="0" max="<?= $max ?>"
                                   name="<?= $subject ?>" class="form-control"
                                   placeholder="Enter score" required>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="padding: 0 24px 24px;">
                    <button type="submit" class="btn btn-primary">Calculate and Save Grade</button>
                </div>
            </form>
        </div>

        <div class="page-links">
            <a href="student_dashboard.php">Back to Menu</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>