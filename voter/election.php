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

// handle vote submission
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
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= e($e['title']) ?> - VoteKDU Election</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      backdrop-filter: blur(20px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    .candidate-card {
      background: rgba(255, 255, 255, 0.07);
      border-radius: 15px;
      backdrop-filter: blur(15px);
      transition: all 0.3s ease;
      overflow: hidden;
    }
    .candidate-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0, 212, 255, 0.2);
    }
    .winner-card {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.5rem;
      border-radius: 15px;
      background: rgba(40, 167, 69, 0.15);
      border: 2px solid #28a745;
      margin-bottom: 2rem;
    }
    .winner-card img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #28a745;
    }
    .progress-bar {
      background: linear-gradient(90deg, #00d4ff, #6366f1, #8b5cf6);
    }
    .site-logo {
      text-align: center;
      margin: 40px 0;
    }
    .site-logo img {
      height: 180px;
      width: auto;
    }
  </style>
</head>
<body>

<!-- Background particles -->
<div id="tsparticles"></div>

<!-- Logo -->
<div class="site-logo">
  <img src="/club_voting/assets/logo.png" alt="VoteKDU Logo">
</div>

<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container py-5">

  <!-- Election info -->
  <div class="glass-card text-center">
    <h1 class="mb-3"><?= e($e['title']) ?></h1>
    <p class="lead"><strong>Club:</strong> <?= e($e['club_name']) ?></p>
    <p><?= e($e['description']) ?></p>
    <p><strong>Starts:</strong> <?= e($e['start_datetime']) ?> | <strong>Ends:</strong> <?= e($e['end_datetime']) ?></p>
  </div>

  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger"><?= e(implode(', ', $errors)) ?></div>
  <?php endif; ?>

  <?php if ($status === 'ongoing'): ?>
    <div class="glass-card">
      <h3 class="mb-4">Cast Your Vote</h3>
      <?php if ($has_voted): ?>
        <div class="alert alert-info">You have already voted in this election.</div>
      <?php else: ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="row g-4">
            <?php foreach ($candidates as $c): ?>
              <div class="col-md-6 col-lg-4">
                <div class="candidate-card p-3 h-100 text-center">
                  <?php if ($c['photo']): ?>
                    <img src="/club_voting/uploads/<?= e($c['photo']) ?>" class="img-fluid rounded mb-3" style="max-height:180px;" alt="Candidate photo">
                  <?php endif; ?>
                  <h5 class="fw-bold"><?= e($c['name']) ?></h5>
                  <p class="small text-light"><?= e($c['bio']) ?></p>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input me-2" type="radio" name="candidate_id" id="cand<?= e($c['id']) ?>" value="<?= e($c['id']) ?>" required>
                    <label class="form-check-label" for="cand<?= e($c['id']) ?>">Select</label>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="text-center mt-4">
            <button class="btn btn-votekdu-primary btn-lg">Submit Vote</button>
          </div>
        </form>
      <?php endif; ?>
    </div>

  <?php elseif ($status === 'upcoming'): ?>
    <div class="glass-card text-center">
      <h3>This election has not started yet</h3>
      <p class="mb-0">It will begin on <strong><?= e($e['start_datetime']) ?></strong></p>
    </div>

  <?php else: // past ?>
    <div class="glass-card">
      <h3 class="mb-4">Election Results</h3>
      <?php if ($results): ?>
        <!-- Winner Highlight -->
        <div class="winner-card mb-4">
          <?php if ($results[0]['photo']): ?>
            <img src="/club_voting/uploads/<?= e($results[0]['photo']) ?>" alt="Winner photo">
          <?php endif; ?>
          <div>
            <h4 class="text-success fw-bold mb-1">🏆 Winner</h4>
            <p class="mb-0"><strong><?= e($results[0]['name']) ?></strong> with <?= e($results[0]['votes']) ?> votes</p>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <ul class="list-group mb-4">
              <?php foreach ($results as $r): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white">
                  <?= e($r['name']) ?>
                  <span class="badge bg-info"><?= e($r['votes']) ?> votes</span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="col-lg-6">
            <canvas id="resultsChart" height="200"></canvas>
          </div>
        </div>
        <script>
          const ctx = document.getElementById('resultsChart').getContext('2d');
          new Chart(ctx, {
            type: 'pie',
            data: {
              labels: <?= json_encode(array_column($results, 'name')) ?>,
              datasets: [{
                data: <?= json_encode(array_column($results, 'votes')) ?>,
                backgroundColor: ['#00d4ff','#6366f1','#8b5cf6','#ff00ff','#ffc300','#20c997','#fd7e14']
              }]
            }
          });
        </script>
      <?php else: ?>
        <p class="text-muted">No votes were cast in this election.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-votekdu-secondary">⬅ Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
  tsParticles.load("tsparticles", {
    background: { color: "transparent" },
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 }},
      color: { value: ["#00d4ff","#ff00ff","#ffffff"] },
      shape: { type: "circle" },
      opacity: { value: 0.6 },
      size: { value: { min: 2, max: 6 }},
      links: { enable: true, distance: 150, color: "#00d4ff", opacity: 0.3, width: 1 },
      move: { enable: true, speed: 1.5, random: true, outModes: { default: "out" }}
    },
    interactivity: {
      events: { onHover: { enable: true, mode: "repulse" }, onClick: { enable: true, mode: "push" }},
      modes: { repulse: { distance: 100 }, push: { quantity: 3 }}
    },
    detectRetina: true
  });
</script>
</body>
</html>
