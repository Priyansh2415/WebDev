<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$count = (int)$conn->query('SELECT COUNT(*) AS total FROM users')->fetch_assoc()['total'];
$error = '';
if ($count > 0) {
    http_response_code(403);
    exit('Setup is locked because a user already exists. Delete install.php now.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    if ($name === '' || !$email || strlen($password) < 10) {
        $error = 'Enter a name, valid email, and password of at least 10 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, 'admin', 'active')");
        $stmt->bind_param('sss', $name, $email, $hash);
        $stmt->execute();
        audit_log($conn, 'initial_admin_created', 'First administrator account created');
        flash('Administrator created. Sign in now.');
        redirect('login.php');
    }
}
$pageTitle = 'Initial Setup';
require __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-7 col-lg-5">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3">Create first administrator</h1>
<p class="text-muted">This page works only while the users table is empty. Remove install.php after setup.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" required maxlength="100"></div>
<div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" required maxlength="190"></div>
<div class="mb-3"><label class="form-label" for="password">Password (minimum 10 characters)</label><input class="form-control" id="password" type="password" name="password" minlength="10" required autocomplete="new-password"></div>
<button class="btn btn-primary w-100">Create administrator</button>
</form>
</div></div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
