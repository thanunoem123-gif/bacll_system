<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$scienceCount = $conn->query("SELECT COUNT(*) AS c FROM students WHERE track='science'")->fetch_assoc()['c'];
$socialCount  = $conn->query("SELECT COUNT(*) AS c FROM students WHERE track='social'")->fetch_assoc()['c'];
?>

<div class="dashboard-wrapper">
    <div class="container">

        <div class="topbar">
            <h2>Review Submissions</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <p style="color:#6b7280; font-size:14px; margin-bottom:24px;">
            Choose a track to view its student records.
        </p>

        <div class="track-grid">
            <a href="admin_track_records.php?track=science" class="track-card science">
                <h3>Science Class Records</h3>
                <p><?= $scienceCount ?> record(s) - Khmer, Math, Biology, Chemistry, Physics, History, English</p>
            </a>

            <a href="admin_track_records.php?track=social" class="track-card social">
                <h3>Social Class Records</h3>
                <p><?= $socialCount ?> record(s) - Khmer, Math, Biology, Chemistry, Physics, History, English</p>
            </a>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>