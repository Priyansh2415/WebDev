<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'staff']);
$pageTitle = 'Dashboard';
$stats = [];
foreach ([
    'Users' => 'SELECT COUNT(*) AS n FROM users',
    'Students' => 'SELECT COUNT(*) AS n FROM students',
    'Events' => 'SELECT COUNT(*) AS n FROM events',
    'Registrations' => 'SELECT COUNT(*) AS n FROM registrations'
] as $label => $sql) {
    $stats[$label] = (int)$conn->query($sql)->fetch_assoc()['n'];
}
$latest = $conn->query('SELECT r.registered_at, s.full_name, e.title FROM registrations r JOIN students s ON s.id=r.student_id JOIN events e ON e.id=r.event_id ORDER BY r.registered_at DESC LIMIT 6');
$monthly = $conn->query("SELECT DATE_FORMAT(registered_at, '%Y-%m') AS month_key, COUNT(*) AS total FROM registrations WHERE registered_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY month_key ORDER BY month_key");
$chartLabels = []; $chartValues = [];
while ($row = $monthly->fetch_assoc()) { $chartLabels[] = $row['month_key']; $chartValues[] = (int)$row['total']; }
audit_log($conn, 'dashboard_view', 'Dashboard opened');
require __DIR__ . '/includes/header.php';
?>
<h1 class="h2 mb-4">Admin Dashboard</h1>
<div class="row g-3 mb-4">
<?php foreach ($stats as $label => $value): ?>
<div class="col-6 col-xl-3"><div class="card stat-card h-100"><div class="card-body"><div class="text-muted"><?= e($label) ?></div><div class="stat-number"><?= $value ?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="row g-4">
<div class="col-lg-7"><div class="card shadow-sm h-100"><div class="card-body">
<h2 class="h5">Registrations by month</h2><canvas id="registrationChart" height="130" aria-label="Registrations by month chart"></canvas>
<script>window.registrationChartData = <?= json_encode(['labels'=>$chartLabels,'values'=>$chartValues], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;</script>
</div></div></div>
<div class="col-lg-5"><div class="card shadow-sm h-100"><div class="card-body">
<h2 class="h5">Latest registrations</h2>
<div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Student</th><th>Event</th><th>Date</th></tr></thead><tbody>
<?php if ($latest->num_rows === 0): ?><tr><td colspan="3">No registrations yet.</td></tr><?php endif; ?>
<?php while ($row = $latest->fetch_assoc()): ?><tr><td><?= e($row['full_name']) ?></td><td><?= e($row['title']) ?></td><td><?= e($row['registered_at']) ?></td></tr><?php endwhile; ?>
</tbody></table></div></div></div></div>
</div>
<div class="alert alert-info mt-4 mb-0">Signed in as <strong><?= e(current_user()['role']) ?></strong>. Menu links and page permissions are controlled by role.</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
