<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'staff']);
$pageTitle = 'Students';
$q = trim((string)($_GET['q'] ?? ''));
$like = '%' . $q . '%';
$stmt = $conn->prepare('SELECT id, full_name, email, course, created_at FROM students WHERE full_name LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY id DESC LIMIT 100');
$stmt->bind_param('sss', $like, $like, $like);
$stmt->execute();
$rows = $stmt->get_result();
audit_log($conn, 'students_view', 'Student list viewed; search used: ' . ($q !== '' ? 'yes' : 'no'));
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><h1 class="h2 mb-0">Students</h1>
<form class="d-flex gap-2" method="get"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search name, email, course" aria-label="Search students"><button class="btn btn-primary">Search</button><a class="btn btn-outline-secondary" href="students.php">Clear</a></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Created</th></tr></thead><tbody>
<?php if ($rows->num_rows === 0): ?><tr><td colspan="5" class="text-center py-4">No students found.</td></tr><?php endif; ?>
<?php while ($row = $rows->fetch_assoc()): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['full_name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['course']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
