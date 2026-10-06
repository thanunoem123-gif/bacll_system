<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$errors = [];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { header("Location: admin_records.php"); exit; }

// Fetch record first to know the track
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$s) {
    echo "<div class='dashboard-wrapper'><div class='container'><div class='alert alert-error'>Record not found.</div></div></div>";
    include 'includes/footer.php';
    exit;
}

$track = $s['track'];
$trackLabel = ($track === 'science') ? 'Science' : 'Social';

if ($track === 'science') {
    $limits = [
        'khmer' => 75, 'math' => 125, 'biology' => 75, 'chemistry' => 75,
        'physics' => 75, 'history' => 50, 'english' => 75,
    ];
    $labels = [
        'khmer' => 'Khmer Literature', 'math' => 'Mathematics',
        'biology' => 'Biology', 'chemistry' => 'Chemistry',
        'physics' => 'Physics', 'history' => 'History', 'english' => 'English',
    ];
} else {
    $limits = [
        'khmer_lit' => 125, 'history' => 75, 'geography' => 75,
        'moral_civic' => 75, 'math' => 75, 'foreign_lang' => 50, 'earth_science' => 50,
    ];
    $labels = [
        'khmer_lit' => 'Khmer Literature', 'history' => 'History',
        'geography' => 'Geography', 'moral_civic' => 'Moral-Civic Education',
        'math' => 'Mathematics', 'foreign_lang' => 'Foreign Language (English)',
        'earth_science' => 'Earth Science (Earth and Ecology)',
    ];
}
$totalMax = array_sum($limits);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid session.";
    }

    $student_id   = trim($_POST['student_id'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');

    if ($student_id === '')   $errors[] = "Student ID required.";
    if ($student_name === '') $errors[] = "Student name required.";

    $scores = [];
    foreach ($limits as $subject => $max) {
        $val = isset($_POST[$subject]) ? (float) $_POST[$subject] : -1;
        if ($val < 0 || $val > $max) {
            $errors[] = $labels[$subject] . " must be 0-$max.";
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

        if ($track === 'science') {
            $stmt = $conn->prepare(
                "UPDATE students SET
                    student_id=?, student_name=?,
                    khmer=?, math=?, biology=?, chemistry=?, physics=?, history=?, english=?,
                    total=?, average=?, grade=?
                 WHERE id=?"
            );
            $stmt->bind_param(
                "ssddddddddssi",
                $student_id, $student_name,
                $scores['khmer'], $scores['math'], $scores['biology'],
                $scores['chemistry'], $scores['physics'], $scores['history'],
                $scores['english'], $total, $average, $grade, $id
            );
        } else {
            $stmt = $conn->prepare(
                "UPDATE students SET
                    student_id=?, student_name=?,
                    khmer_lit=?, history=?, geography=?, moral_civic=?, math=?, foreign_lang=?, earth_science=?,
                    total=?, average=?, grade=?
                 WHERE id=?"
            );
            $stmt->bind_param(
                "ssddddddddssi",
                $student_id, $student_name,
                $scores['khmer_lit'], $scores['history'], $scores['geography'],
                $scores['moral_civic'], $scores['math'], $scores['foreign_lang'],
                $scores['earth_science'], $total, $average, $grade, $id
            );
        }

        if ($stmt->execute()) {
            header("Location: admin_track_records.php?track=$track&msg=Record updated successfully");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="dashboard-wrapper">
    <div class="container">

        <div class="topbar">
            <h2>Edit <?= $trackLabel ?> Student</h2>
            <a href="admin_track_records.php?track=<?= $track ?>" class="btn btn-secondary">Back</a>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="grade-grid two-col">
                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="student_id" class="form-control"
                               value="<?= htmlspecialchars($s['student_id']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Student Name *</label>
                        <input type="text" name="student_name" class="form-control"
                               value="<?= htmlspecialchars($s['student_name']) ?>" required>
                    </div>
                </div>

                <div class="grade-grid" style="grid-template-columns: 1fr; padding-top: 0;">
                    <?php foreach ($limits as $subject => $max): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($labels[$subject]) ?> (0-<?= $max ?>)</label>
                            <input type="number" step="0.01" min="0" max="<?= $max ?>"
                                   name="<?= $subject ?>" class="form-control"
                                   value="<?= htmlspecialchars($s[$subject] ?? '') ?>" required>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="padding: 0 24px 24px;">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>