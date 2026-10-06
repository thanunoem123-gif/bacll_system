<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}
?>

<div class="dashboard-wrapper">
    <div class="container">

        <div class="dash-header">
            <h1>Student Dashboard</h1>
            <p class="desc">Choose a class track to calculate grades.</p>
            <div class="badge-joined">
                Joined by <?= htmlspecialchars($_SESSION['username']) ?> on <?= htmlspecialchars($_SESSION['joined_at']) ?>
            </div>
            <div class="dash-actions">
                <a href="logout.php" class="btn btn-logout">Logout</a>
                <a href="users.php" class="btn btn-green">View User Data</a>
            </div>
        </div>

        <div class="track-grid">
            <a href="science_track.php" class="track-card science">
                <h3>Science Class Track</h3>
                <p>Khmer, Math, Biology, Chemistry, Physics, History, English</p>
            </a>

            <a href="social_track.php" class="track-card social">
                <h3>Social Class Track</h3>
                <p>Khmer, Math, Biology, Chemistry, Physics, History, English</p>
            </a>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>