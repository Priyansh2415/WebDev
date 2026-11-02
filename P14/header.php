<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
$flashMessage = take_flash();
$menus = [
    ['label' => 'Dashboard', 'url' => 'index.php', 'roles' => ['admin', 'staff']],
    ['label' => 'Students', 'url' => 'students.php', 'roles' => ['admin', 'staff']],
    ['label' => 'Registrations', 'url' => 'registrations.php', 'roles' => ['admin', 'staff']],
    ['label' => 'User Management', 'url' => 'users.php', 'roles' => ['admin']],
    ['label' => 'Audit Log', 'url' => 'audit.php', 'roles' => ['admin']],
];
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Admin Dashboard') ?> | Practical 14</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Practical 14</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav">
      <?php if ($user): ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php foreach ($menus as $menu): ?>
          <?php if (in_array($user['role'], $menu['roles'], true)): ?>
          <li class="nav-item"><a class="nav-link <?= $currentPage === $menu['url'] ? 'active' : '' ?>" href="<?= e($menu['url']) ?>"><?= e($menu['label']) ?></a></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
      <span class="navbar-text text-white me-3"><?= e($user['name']) ?> (<?= e($user['role']) ?>)</span>
      <form method="post" action="logout.php" class="d-inline">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="btn btn-outline-light btn-sm" type="submit">Log out</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($flashMessage): ?>
  <div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible fade show" role="alert">
    <?= e($flashMessage['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
