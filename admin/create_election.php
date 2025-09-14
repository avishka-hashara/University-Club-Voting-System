<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $start = trim($_POST['start_datetime'] ?? '');
    $end = trim($_POST['end_datetime'] ?? '');
    if (!$title) $errors[] = 'Title required';
    if (!$start || !$end) $errors[] = 'Start and end times required';
    if (new DateTime($start) >= new DateTime($end)) $errors[] = 'Start must be before end';
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO elections (club_id, title, description, start_datetime, end_datetime, is_active, created_by) VALUES (?, ?, ?, ?, ?, 0, ?)");
        $stmt->execute([$club_id, $title, $desc, $start, $end, current_user_id()]);
        flash_set('success', 'Election created');
        header('Location: /club_voting/admin/dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Election - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-4">
  <h1>Create Election</h1>
  <?php if ($errors): ?><div class="alert alert-danger"><?= e(implode(', ', $errors)) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Start (YYYY-MM-DD HH:MM:SS)</label>
      <input name="start_datetime" class="form-control" required placeholder="2025-01-01 09:00:00" value="<?= e(date('Y-m-d H:i:00', time()+3600)) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">End (YYYY-MM-DD HH:MM:SS)</label>
      <input name="end_datetime" class="form-control" required placeholder="2025-01-01 17:00:00" value="<?= e(date('Y-m-d H:i:00', time()+3600*6)) ?>">
    </div>
    <button class="btn btn-primary">Create</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
