<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$track = $_GET['track'] ?? '';
if ($track !== 'science' && $track !== 'social') {
    header("Location: admin_records.php");
    exit;
}

$trackLabel = ($track === 'science') ? 'Science' : 'Social';
$search     = trim($_GET['search'] ?? '');

// Column definitions per track
$columns = ($track === 'science')
    ? [
        'khmer'     => 'Khmer',
        'math'      => 'Math',
        'biology'   => 'Bio',
        'chemistry' => 'Chem',
        'physics'   => 'Phys',
        'history'   => 'Hist',
        'english'   => 'Eng',
    ]
    : [
        'khmer_lit'     => 'Khmer Lit',
        'history'       => 'History',
        'geography'     => 'Geography',
        'moral_civic'   => 'Moral',
        'math'          => 'Math',
        'foreign_lang'  => 'Foreign',
        'earth_science' => 'Earth Sci',
    ];

// Build query with optional search
$where  = ["track = ?"];
$params = [$track];
$types  = 's';

if ($search !== '') {
    $where[]  = "(student_name LIKE ? OR student_id LIKE ?)";
    $like     = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

$sql = "SELECT * FROM students WHERE " . implode(" AND ", $where) . " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$records = $stmt->get_result();
$count   = $records->num_rows;

$totalMax = ($track === 'science') ? 550 : 525;
?>

<div class="dashboard-wrapper">
    <div class="container">

        <div class="topbar">
            <h2><?= $trackLabel ?> Class Records</h2>
            <div class="topbar-actions">
                <a href="admin_add_student.php?track=<?= $track ?>" class="btn btn-secondary">+ Add <?= $trackLabel ?> Student</a>
                <a href="admin_records.php" class="btn btn-secondary">Change Track</a>
                <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-bar">
            <input type="hidden" name="track" value="<?= htmlspecialchars($track) ?>">
            <div class="form-group">
                <label>Search by name or student ID</label>
                <input type="text" name="search" class="form-control"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="e.g. Sok Dara or STU-001">
            </div>
            <button type="submit" class="btn btn-primary" style="width:auto; padding:13px 28px;">Search</button>
            <?php if ($search !== ''): ?>
                <a href="admin_track_records.php?track=<?= $track ?>" class="btn btn-secondary" style="padding:13px 28px;">Clear</a>
            <?php endif; ?>
        </form>

        <p style="color:#6b7280; font-size:14px; margin-bottom:16px;">
            <?php if ($search !== ''): ?>
                Found <strong><?= $count ?></strong> matching record(s) for "<strong><?= htmlspecialchars($search) ?></strong>" in the <?= $trackLabel ?> class.
            <?php else: ?>
                <?= $count ?> record(s) found in the <?= $trackLabel ?> class.
            <?php endif; ?>
        </p>

        <div class="table-wrapper" style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <?php foreach ($columns as $label): ?>
                            <th><?= $label ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                        <th>Avg %</th>
                        <th>Grade</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($records->num_rows > 0): ?>
                    <?php while ($r = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['student_id']) ?></td>
                            <td><?= htmlspecialchars($r['student_name']) ?></td>
                            <?php foreach ($columns as $col => $label): ?>
                                <td><?= $r[$col] !== null ? $r[$col] : '-' ?></td>
                            <?php endforeach; ?>
                            <td><strong><?= $r['total'] ?></strong>/<?= $totalMax ?></td>
                            <td><?= number_format($r['average'], 2) ?></td>
                            <td><span class="grade-badge grade-<?= $r['grade'] ?>"><?= $r['grade'] ?></span></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                            <td style="white-space:nowrap;">
                                <a href="admin_edit_student.php?id=<?= $r['id'] ?>&track=<?= $track ?>"
                                   class="btn btn-secondary" style="padding:6px 10px; font-size:12px;">Edit</a>
                                <form method="POST" action="admin_delete_student.php" style="display:inline;"
                                      onsubmit="return confirm('Delete this student record?');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="track" value="<?= $track ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($columns) + 7 ?>">
                            <div class="empty-state">
                                <?php if ($search !== ''): ?>
                                    <h3>No matching records</h3>
                                    <p>No <?= $trackLabel ?> student matches "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                                    <p style="margin-top:12px;">
                                        <a href="admin_track_records.php?track=<?= $track ?>" style="color:#374151; text-decoration:underline;">Clear search</a>
                                    </p>
                                <?php else: ?>
                                    <h3>No <?= $trackLabel ?> records yet</h3>
                                    <p>Click "Add <?= $trackLabel ?> Student" to create the first record.</p>
                                <?php endif; ?>
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
include 'includes/footer.php';
?>