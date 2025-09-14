<?php
require_once __DIR__ . '/../init.php';
require_role('voter');

$election_id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT e.*, c.name as club_name FROM elections e JOIN clubs c ON e.club_id = c.id WHERE e.id = ?");
$stmt->execute([$election_id]);
$e = $stmt->fetch();
if (!$e) { echo "Election not found."; exit; }

// fetch candidates
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE election_id = ?");
$stmt->execute([$election_id]);
$candidates = $stmt->fetchAll();

// check if user already voted
$stmt = $pdo->prepare("SELECT * FROM votes WHERE election_id = ? AND user_id = ?");
$stmt->execute([$election_id, current_user_id()]);
$has_voted = (bool)$stmt->fetch();

$now = date('Y-m-d H:i:s');
if ($now < $e['start_datetime']) {
    $status = 'upcoming';
} elseif ($now >= $e['start_datetime'] && $now <= $e['end_datetime']) {
    $status = $e['is_active'] ? 'ongoing' : 'past'; 
} else {
    $status = 'past';
}

// handle vote
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if ($status !== 'ongoing') $errors[] = 'Election not open for voting';
    if ($has_voted) $errors[] = 'You have already voted';
    $candidate_id = intval($_POST['candidate_id'] ?? 0);

    $stmt = $pdo->prepare("SELECT id FROM candidates WHERE id = ? AND election_id = ?");
    $stmt->execute([$candidate_id, $election_id]);
    if (!$stmt->fetch()) $errors[] = 'Invalid candidate';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            $ins = $pdo->prepare("INSERT INTO votes (election_id, candidate_id, user_id) VALUES (?, ?, ?)");
            $ins->execute([$election_id, $candidate_id, current_user_id()]);
            $pdo->commit();
            flash_set('success', 'Vote cast successfully');
            header("Location: /club_voting/voter/election.php?id={$election_id}");
            exit;
        } catch (PDOException $ex) {
            $pdo->rollBack();
            $errors[] = 'Unable to cast vote (maybe you already voted).';
        }
    }
}

// results
$results = [];
if ($status === 'past') {
    $sql = "SELECT c.id, c.name, c.photo, COUNT(v.id) as votes 
            FROM candidates c 
            LEFT JOIN votes v ON c.id = v.candidate_id 
            WHERE c.election_id = ? 
            GROUP BY c.id 
            ORDER BY votes DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    $results = $stmt->fetchAll();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= e($e['title']) ?> - Election</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 1rem; }
    .winner-card {
      background: #e9f7ef;
      border-left: 6px solid #28a745;
      padding: 1rem;
      border-radius: .75rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .winner-card img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #28a745;
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-5">
  <div class="text-center mb-4">
    <h1 class="fw-bold"><?= e($e['title']) ?></h1>
    <p class="lead text-muted"><?= e($e['club_name']) ?> | <?= e($e['description']) ?></p>
    <p><strong>Starts:</strong> <?= e($e['start_datetime']) ?> | <strong>Ends:</strong> <?= e($e['end_datetime']) ?></p>
  </div>
  <hr>

  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success text-center"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger text-center"><?= e(implode(', ', $errors)) ?></div>
  <?php endif; ?>

  <?php if ($status === 'ongoing'): ?>
    <!-- Progress -->
    <?php
      $start = strtotime($e['start_datetime']);
      $end = strtotime($e['end_datetime']);
      $total = $end - $start;
      $elapsed = time() - $start;
      $progress = max(0, min(100, ($elapsed / $total) * 100));
    ?>
    <div class="mb-4">
      <label><strong>Time Progress:</strong></label>
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
             role="progressbar" style="width: <?= $progress ?>%">
          <?= round($progress) ?>%
        </div>
      </div>
    </div>

    <h3 class="mb-3">Cast your vote</h3>
    <?php if ($has_voted): ?>
      <div class="alert alert-info text-center">You have already voted in this election.</div>
    <?php else: ?>
      <form method="post" class="row">
        <?= csrf_field() ?>
        <?php foreach ($candidates as $c): ?>
          <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
              <?php if ($c['photo']): ?>
                <img src="/club_voting/uploads/<?= e($c['photo']) ?>" class="card-img-top" alt="Candidate photo">
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= e($c['name']) ?></h5>
                <p class="card-text text-muted"><?= e($c['bio']) ?></p>
                <input class="form-check-input me-2" type="radio" name="candidate_id" 
                       id="cand<?= e($c['id']) ?>" value="<?= e($c['id']) ?>" required>
                <label class="form-check-label" for="cand<?= e($c['id']) ?>">Select</label>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="text-center">
          <button class="btn btn-success px-4">Vote</button>
        </div>
      </form>
    <?php endif; ?>

  <?php elseif ($status === 'upcoming'): ?>
    <div class="alert alert-info text-center">This election starts at <?= e($e['start_datetime']) ?></div>

  <?php else: // past ?>
    <h3 class="mb-3">Results</h3>
    <?php if ($results): ?>
      <table class="table table-bordered table-striped shadow-sm">
        <thead class="table-dark">
          <tr><th>Candidate</th><th>Votes</th></tr>
        </thead>
        <tbody>
          <?php foreach ($results as $r): ?>
            <tr><td><?= e($r['name']) ?></td><td><?= e($r['votes']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Winner Highlight -->
      <div class="winner-card mt-4">
        <?php if ($results[0]['photo']): ?>
          <img src="/club_voting/uploads/<?= e($results[0]['photo']) ?>" alt="Winner photo">
        <?php endif; ?>
        <div>
          <h4 class="mb-1 text-success fw-bold">🏆 Winner</h4>
          <p class="mb-0"><strong><?= e($results[0]['name']) ?></strong> with <?= e($results[0]['votes']) ?> votes</p>
        </div>
      </div>

      <!-- Chart -->
      <canvas id="resultsChart" class="mt-4" height="200"></canvas>
      <script>
        const ctx = document.getElementById('resultsChart').getContext('2d');
        new Chart(ctx, {
          type: 'pie',
          data: {
            labels: <?= json_encode(array_column($results, 'name')) ?>,
            datasets: [{
              data: <?= json_encode(array_column($results, 'votes')) ?>,
              backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#20c997','#fd7e14']
            }]
          }
        });
      </script>
    <?php else: ?>
      <p>No votes were cast.</p>
    <?php endif; ?>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
  </div>
</div>
</body>
</html>
