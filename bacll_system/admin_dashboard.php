<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$totalUsers    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='student'")->fetch_assoc()['c'];
$totalStudents = $conn->query("SELECT COUNT(*) AS c FROM students")->fetch_assoc()['c'];
$scienceCount  = $conn->query("SELECT COUNT(*) AS c FROM students WHERE track='science'")->fetch_assoc()['c'];
$socialCount   = $conn->query("SELECT COUNT(*) AS c FROM students WHERE track='social'")->fetch_assoc()['c'];
?>

<div class="dashboard-wrapper">
    <div class="container">

        <div class="dash-header">
            <h1>Admin Dashboard</h1>
            <p class="desc">Review submitted grades and registered accounts.</p>
            <div class="badge-joined">
                Joined by <?= htmlspecialchars($_SESSION['username']) ?> on <?= htmlspecialchars($_SESSION['joined_at']) ?>
            </div>
            <div class="dash-actions">
                <a href="logout.php" class="btn btn-logout">Logout</a>
                <a href="users.php" class="btn btn-secondary">View User Data</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">Students Registered</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalStudents ?></div>
                <div class="stat-label">Grade Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $scienceCount ?></div>
                <div class="stat-label">Science Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $socialCount ?></div>
                <div class="stat-label">Social Students</div>
            </div>
        </div>

        <div class="topbar" style="margin-top:32px;">
            <h2>Records</h2>
        </div>

        <a href="admin_records.php" class="track-card" style="display:block;">
            <h3>Review Science and Social Submissions</h3>
            <p>Registered accounts, grade submissions, and student records - choose a track to view, add, edit, or delete</p>
        </a>

    </div>
</div>

<?php include 'includes/footer.php'; ?>