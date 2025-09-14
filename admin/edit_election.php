<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND club_id = ?");
$stmt->execute([$id, $club_id]);
$e = $stmt->fetch();
if (!$e) { echo "Election not found."; exit; }

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
        $stmt = $pdo->prepare("UPDATE elections SET title=?, description=?, start_datetime=?, end_datetime=? WHERE id=? AND club_id=?");
        $stmt->execute([$title, $desc, $start, $end, $id, $club_id]);
        flash_set('success','Election updated');
        header('Location: /club_voting/admin/dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Election - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-4">
  <h1>Edit Election</h1>
  <?php if ($errors): ?><div class="alert alert-danger"><?= e(implode(', ', $errors)) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label>Title</label>
      <input name="title" class="form-control" value="<?= e($e['title']) ?>">
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= e($e['description']) ?></textarea>
    </div>
    <div class="mb-3">
      <label>Start</label>
      <input name="start_datetime" class="form-control" value="<?= e($e['start_datetime']) ?>">
    </div>
    <div class="mb-3">
      <label>End</label>
      <input name="end_datetime" class="form-control" value="<?= e($e['end_datetime']) ?>">
    </div>
    <button class="btn btn-primary">Save</button>
  </form>
</div>
</body>
</html>
