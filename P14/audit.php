<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_role(['admin']);
$pageTitle = 'Audit Log';
$result = $conn->query('SELECT id, actor_name, action, details, ip_address, created_at FROM audit_logs ORDER BY id DESC LIMIT 250');
require __DIR__ . '/includes/header.php';
?>
<h1 class="h2 mb-3">Audit Log</h1>
<p class="text-muted">Showing the latest 250 recorded events. Access is restricted to administrators.</p>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped table-sm mb-0"><thead><tr><th>ID</th><th>Actor</th><th>Action</th><th>Details</th><th>IP address</th><th>Timestamp</th></tr></thead><tbody>
<?php if ($result->num_rows === 0): ?><tr><td colspan="6">No audit events yet.</td></tr><?php endif; ?>
<?php while ($row = $result->fetch_assoc()): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['actor_name']) ?></td><td><?= e($row['action']) ?></td><td><?= e($row['details']) ?></td><td><?= e($row['ip_address']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
