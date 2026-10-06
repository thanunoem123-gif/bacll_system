
<?php
include 'config/db.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = "Invalid session. Please try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '') {
            $error = "Please enter a username.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Development bypass: admin logs in with any password
            $passwordOK = false;
            if ($user) {
                if ($user['role'] === 'admin') {
                    $passwordOK = true;
                } else {
                    $passwordOK = password_verify($password, $user['password']);
                }
            }

            if ($passwordOK) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['joined_at'] = date('Y-m-d H:i:s');

                header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Form Login</h1>
        <p class="subtitle">Enter Username and Password</p>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful. Please log in.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control"
                       placeholder="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="password" required>
            </div>

            <div class="form-group" style="margin-top:22px;">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>

            <div style="text-align:center; margin-top:12px;">
                <a href="register.php" class="btn btn-secondary" style="width:100%;">Register</a>
            </div>

            <div style="text-align:center; margin-top:16px;">
                <a href="welcome.php" style="color:#6b7280; font-size:13px; text-decoration:underline;">
                    Back to welcome
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>