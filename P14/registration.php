<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'staff']);
$pageTitle = 'Event Registrations';
$result = $conn->query('SELECT r.id, s.full_name, s.email, e.title, e.event_date, r.registered_at FROM registrations r JOIN students s ON s.id=r.student_id JOIN events e ON e.id=r.event_id ORDER BY r.registered_at DESC LIMIT 200');
audit_log($conn, 'registrations_view', 'Event registrations viewed');
require __DIR__ . '/includes/header.php';
?>
<h1 class="h2 mb-3">Event Registrations</h1>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Student</th><th>Email</th><th>Event</th><th>Event date</th><th>Registered at</th></tr></thead><tbody>
<?php if ($result->num_rows === 0): ?><tr><td colspan="6">No registrations yet.</td></tr><?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['full_name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['title']) ?></td><td><?= e($row['event_date']) ?></td><td><?= e($row['registered_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

