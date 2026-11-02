<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_role(['admin']);
$pageTitle = 'User Management';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? 'staff');
        if ($name === '' || !$email || strlen($password) < 10 || !in_array($role, ['admin', 'staff'], true)) {
            $error = 'Enter valid details and a password of at least 10 characters.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $name, $email, $hash, $role);
                $stmt->execute();
                audit_log($conn, 'user_created', 'Created account for ' . $email . ' with role ' . $role);
                flash('User created successfully.');
                redirect('users.php');
            } catch (mysqli_sql_exception $ex) {
                $error = $ex->getCode() === 1062 ? 'That email address is already registered.' : 'Unable to create user.';
            }
        }
    } elseif ($action === 'toggle') {
        $targetId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        if (!$targetId || $targetId === (int)current_user()['id']) {
            $error = 'You cannot change your own account status here.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE id = ?");
            $stmt->bind_param('i', $targetId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) audit_log($conn, 'user_status_toggled', 'Changed status for user ID ' . $targetId);
            flash($stmt->affected_rows > 0 ? 'User status updated.' : 'No user status change was made.', $stmt->affected_rows > 0 ? 'success' : 'warning');
            redirect('users.php');
        }
    }
}
$result = $conn->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC');
require __DIR__ . '/includes/header.php';
?>
<h1 class="h2 mb-3">User Management</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card shadow-sm mb-4"><div class="card-body"><h2 class="h5">Create user</h2>
<form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create">
<div class="col-md-3"><label class="form-label">Name</label><input class="form-control" name="name" maxlength="100" required></div>
<div class="col-md-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" maxlength="190" required></div>
<div class="col-md-3"><label class="form-label">Temporary password</label><input class="form-control" type="password" name="password" minlength="10" required autocomplete="new-password"></div>
<div class="col-md-2"><label class="form-label">Role</label><select class="form-select" name="role"><option value="staff">Staff</option><option value="admin">Admin</option></select></div>
<div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary">Add</button></div>
</form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Action</th></tr></thead><tbody>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['role']) ?></td><td><span class="badge text-bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($row['status']) ?></span></td><td><?= e($row['created_at']) ?></td><td>
<?php if ((int)$row['id'] !== (int)current_user()['id']): ?><form method="post" class="d-inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>"><button class="btn btn-sm btn-outline-warning" data-confirm="Change this user's active status?">Toggle status</button></form><?php else: ?><span class="text-muted small">Current user</span><?php endif; ?>
</td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php';?>
