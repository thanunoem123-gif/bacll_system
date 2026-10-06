<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$isAdmin = $_SESSION['role'] === 'admin';

$track  = $_GET['track']  ?? '';
$search = trim($_GET['search'] ?? '');

$where  = [];
$params = [];
$types  = '';

if ($track === 'science' || $track === 'social') {
    $where[] = "track = ?";
    $params[] = $track;
    $types .= 's';
}
if ($search !== '') {
    $where[] = "(student_name LIKE ? OR student_id LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

$sql = "SELECT * FROM students";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result();
?>

<div class="dashboard-wrapper">
    <div class="container">
        <div class="topbar">
            <h2>Student Records</h2>
            <div class="topbar-actions">
                <a href="science_track.php" class="btn btn-secondary">Add Science</a>
                <a href="social_track.php"  class="btn btn-secondary">Add Social</a>
                <a href="<?= $isAdmin ? 'admin_dashboard.php' : 'student_dashboard.php' ?>" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-bar">
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" class="form-control"
                       value="<?= htmlspecialchars($search) ?>" placeholder="Name or student ID">
            </div>
            <div class="form-group" style="max-width:180px;">
                <label>Track</label>
                <select name="track" class="form-control">
                    <option value="">All Tracks</option>
                    <option value="science" <?= $track==='science'?'selected':'' ?>>Science</option>
                    <option value="social"  <?= $track==='social' ?'selected':'' ?>>Social</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:auto; padding:13px 28px;">Filter</button>
        </form>

        <div class="table-wrapper" style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Track</th>
                        <th>Khmer</th>
                        <th>Math</th>
                        <th>Bio</th>
                        <th>Chem</th>
                        <th>Phys</th>
                        <th>Hist</th>
                        <th>Eng</th>
                        <th>Total</th>
                        <th>Avg %</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($rows->num_rows > 0): ?>
                    <?php while ($r = $rows->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['student_id']) ?></td>
                            <td><?= htmlspecialchars($r['student_name']) ?></td>
                            <td><span class="track-pill <?= $r['track'] ?>"><?= $r['track'] ?></span></td>
                            <td><?= $r['khmer'] ?></td>
                            <td><?= $r['math'] ?></td>
                            <td><?= $r['biology'] ?></td>
                            <td><?= $r['chemistry'] ?></td>
                            <td><?= $r['physics'] ?></td>
                            <td><?= $r['history'] ?></td>
                            <td><?= $r['english'] ?></td>
                            <td><strong><?= $r['total'] ?></strong>/550</td>
                            <td><?= number_format($r['average'], 2) ?></td>
                            <td><span class="grade-badge grade-<?= $r['grade'] ?>"><?= $r['grade'] ?></span></td>
                            <td style="white-space:nowrap;">
                                <a href="edit_student.php?id=<?= $r['id'] ?>" class="btn btn-secondary" style="padding:6px 10px; font-size:12px;">Edit</a>
                                <form method="POST" action="delete_student.php" style="display:inline;"
                                      onsubmit="return confirm('Delete this student record?');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="15">
                            <div class="empty-state">
                                <h3>No student records yet</h3>
                                <p>Add one from the Science or Social track.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>