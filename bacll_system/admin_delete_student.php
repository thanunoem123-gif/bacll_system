<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_records.php");
    exit;
}

if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    header("Location: admin_records.php?msg=Invalid session");
    exit;
}

$id    = (int) ($_POST['id'] ?? 0);
$track = $_POST['track'] ?? 'science';
if ($track !== 'science' && $track !== 'social') $track = 'science';

if ($id <= 0) {
    header("Location: admin_track_records.php?track=$track&msg=Invalid ID");
    exit;
}

$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: admin_track_records.php?track=$track&msg=Record deleted successfully");
} else {
    header("Location: admin_track_records.php?track=$track&msg=Record not found");
}
$stmt->close();
$conn->close();
exit;
?>