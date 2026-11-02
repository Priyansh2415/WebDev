<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $conn->prepare('SELECT id, name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($record && $record['status'] === 'active' && password_verify($password, $record['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$record['id'], 'name' => $record['name'], 'email' => $record['email'], 'role' => $record['role']];
        audit_log($conn, 'login_success', 'User signed in');
        redirect('index.php');
    }
    $error = 'Invalid email or password, or the account is inactive.';
    audit_log($conn, 'login_failed', 'Failed login attempt for supplied email');
}
$pageTitle = 'Sign In';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3 mb-3">Sign in</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" autocomplete="username" required></div>
<div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" autocomplete="current-password" required></div>
<button class="btn btn-primary w-100">Sign in</button>
</form>
<p class="small text-muted mt-3 mb-0">First run? Open <code>install.php</code> to create the initial administrator.</p>
</div></div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
