<?php
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$isAdmin = $_SESSION['role'] === 'admin';

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $delId = (int) $_POST['delete_id'];
        $del = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $del->bind_param("i", $delId);
        $del->execute();
        $del->close();
        header("Location: users.php?msg=User deleted");
        exit;
    }
}

$users = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
$backLink = $isAdmin ? 'admin_dashboard.php' : 'student_dashboard.php';
?>

<div class="dashboard-wrapper">
    <div class="container">
        <div class="topbar">
            <h2>View User Data</h2>
            <a href="<?= $backLink ?>" class="btn btn-secondary">Back</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <?php if ($isAdmin): ?><th>Action</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= ucfirst(htmlspecialchars($u['role'])) ?></td>
                            <td><?= htmlspecialchars($u['created_at']) ?></td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <?php if ($u['role'] !== 'admin'): ?>
                                    <form method="POST" style="display:inline;"
                                          onsubmit="return confirm('Delete this user?');">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    <?php else: ?>
                                        <span style="color:#9ca3af; font-size:12px;">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isAdmin ? 5 : 4 ?>" style="text-align:center; color:#9ca3af;">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>