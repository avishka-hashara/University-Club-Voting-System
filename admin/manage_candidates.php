<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];
$election_id = intval($_GET['election_id'] ?? 0);

// verify election belongs to this admin
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND club_id = ?");
$stmt->execute([$election_id, $club_id]);
$election = $stmt->fetch();
if (!$election) { echo "Election not found or not authorized."; exit; }

// handle delete candidate
if (isset($_GET['delete']) && intval($_GET['delete'])) {
    if (!hash_equals($_GET['token'] ?? '', csrf_token())) die('Invalid token');
    $cid = intval($_GET['delete']);
    $del = $pdo->prepare("DELETE FROM candidates WHERE id = ? AND election_id = ?");
    $del->execute([$cid, $election_id]);
    header("Location: manage_candidates.php?election_id={$election_id}");
    exit;
}

// fetch candidates
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE election_id = ?");
$stmt->execute([$election_id]);
$candidates = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Candidates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-4">
  <h1>Candidates for <?= e($election['title']) ?></h1>
  <a href="upload_candidate.php?election_id=<?= e($election_id) ?>" class="btn btn-success mb-3">Add Candidate</a>
  <table class="table">
    <thead><tr><th>Name</th><th>Bio</th><th>Photo</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($candidates as $c): ?>
      <tr>
        <td><?= e($c['name']) ?></td>
        <td><?= e($c['bio']) ?></td>
        <td>
          <?php if ($c['photo']): ?>
            <img src="/club_voting/uploads/<?= e($c['photo']) ?>" style="height:60px;">
          <?php endif; ?>
        </td>
        <td>
          <a class="btn btn-sm btn-warning" href="manage_candidates.php?election_id=<?= e($election_id) ?>&delete=<?= e($c['id']) ?>&token=<?= e(csrf_token()) ?>" onclick="return confirm('Delete candidate?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($candidates)): ?>
      <tr><td colspan="4">No candidates yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
