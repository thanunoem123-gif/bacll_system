<?php
include 'config/db.php';
include 'includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid session.";
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if (strlen($username) < 3) $errors[] = "Username must be at least 3 characters.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "Username already taken.";
        }
        $check->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
        $stmt->bind_param("ss", $username, $hash);

        if ($stmt->execute()) {
            header("Location: index.php?registered=1");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="subtitle">Register a new student account</p>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm" class="form-control" placeholder="confirm password" required>
            </div>

            <div class="form-group" style="margin-top:22px;">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>

            <div style="text-align:center; margin-top:12px;">
                <a href="index.php" class="btn btn-secondary" style="width:100%;">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>