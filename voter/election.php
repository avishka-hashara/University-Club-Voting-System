<?php
require_once __DIR__ . '/../init.php';
require_role('voter');

$election_id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
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
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; background: linear-gradient(135deg,#0a0a0f 0%,#1a1a2e 50%,#16213e 100%); color:#fff; min-height:100vh; overflow-x:hidden; }
    .container { max-width:1200px; margin:0 auto; padding:2rem 1rem; position:relative; z-index:2; }
    .glass-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; backdrop-filter: blur(20px); box-shadow:0 8px 25px rgba(0,0,0,0.4); padding:2rem; margin-bottom:2rem; }
    .candidate-card { background: rgba(255,255,255,0.07); border-radius:15px; backdrop-filter:blur(15px); transition:all 0.3s ease; overflow:hidden; cursor:pointer; margin-bottom:1.5rem; }
    .candidate-card:hover { transform:translateY(-8px); box-shadow:0 15px 35px rgba(0,212,255,0.2); }
    .candidate-card.selected { border:3px solid #00d4ff; box-shadow:0 0 20px rgba(0,212,255,0.5); }
    .winner-card { display:flex; align-items:center; gap:1rem; padding:1.5rem; border-radius:15px; background: rgba(40,167,69,0.15); border:2px solid #28a745; margin-bottom:2rem; }
    .winner-card img { width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #28a745; }
    .btn-votekdu-primary { background:linear-gradient(135deg,#00d4ff,#6366f1); color:#fff; border:none; padding:.7rem 1.5rem; border-radius:10px; cursor:pointer; font-weight:600; transition:all .3s ease; }
    .btn-votekdu-primary:hover { background: linear-gradient(135deg,#0099cc,#4f46e5); }
    .btn-votekdu-secondary { background: rgba(255,255,255,0.1); color:#00d4ff; border:1px solid #00d4ff; padding:.7rem 1.5rem; border-radius:10px; cursor:pointer; font-weight:600; transition:all .3s ease; text-decoration:none; display:inline-block; }
    .btn-votekdu-secondary:hover { background:#00d4ff; color:#fff; }
    .vote-bars { display:flex; flex-direction:column; gap:1rem; margin-top:1.5rem; }
    .vote-bar { display:flex; flex-direction:column; gap:.3rem; }
    .vote-info { display:flex; justify-content:space-between; font-weight:600; }
    .bar-container { background: rgba(255,255,255,0.1); border-radius:12px; height:24px; overflow:hidden; position:relative; }
    .bar-fill { height:100%; border-radius:12px; width:0%; transition: width 1.5s ease-in-out; position:relative; }
    .bar-fill::after { content: attr(data-percent) "%"; position:absolute; right:8px; top:0; height:100%; display:flex; align-items:center; color:#fff; font-weight:600; font-size:0.9rem; }
  </style>
</head>
<body>
<div id="tsparticles"></div>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">

  <!-- Election info -->
  <div class="glass-card text-center">
    <h1><?= e($e['title']) ?></h1>
    <p><?= e($e['description']) ?></p>
    <p><strong>Starts:</strong> <?= e($e['start_datetime']) ?> | <strong>Ends:</strong> <?= e($e['end_datetime']) ?></p>
  </div>

  <?php if ($msg = flash_get('success')): ?>
    <div class="glass-card text-center" style="background: rgba(40,167,69,0.15); border:2px solid #28a745;"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="glass-card text-center" style="background: rgba(220,53,69,0.15); border:2px solid #dc3545;"><?= e(implode(', ', $errors)) ?></div>
  <?php endif; ?>

  <?php if ($status === 'ongoing'): ?>
    <div class="glass-card">
      <h3 class="text-center">Cast Your Vote</h3>
      <?php if ($has_voted): ?>
        <div class="glass-card text-center" style="background: rgba(0,123,255,0.15); border:2px solid #007bff;">You have already voted in this election.</div>
      <?php else: ?>
        <form method="post" id="voteForm">
          <?= csrf_field() ?>
          <div class="candidates-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap:1.5rem;">
          <?php foreach($candidates as $c): ?>
            <div class="candidate-card p-3 text-center" data-candidate-id="<?= e($c['id']) ?>">
              <?php if($c['photo']): ?>
                <img src="/club_voting/uploads/<?= e($c['photo']) ?>" alt="Candidate" style="max-height:180px; border-radius:10px; margin-bottom:1rem;">
              <?php endif; ?>
              <h5><?= e($c['name']) ?></h5>
              <p><?= e($c['bio']) ?></p>
              <input type="radio" name="candidate_id" id="cand<?= e($c['id']) ?>" value="<?= e($c['id']) ?>" required style="display:none;">
            </div>
          <?php endforeach; ?>
          </div>
          <div class="text-center mt-3">
            <button type="submit" class="btn-votekdu-primary">Submit Vote</button>
          </div>
        </form>

        <script>
          const cards = document.querySelectorAll('.candidate-card');
          cards.forEach(card => {
            card.addEventListener('click', () => {
              cards.forEach(c => c.classList.remove('selected'));
              card.classList.add('selected');
              card.querySelector('input[type="radio"]').checked = true;
            });
          });
        </script>

      <?php endif; ?>
    </div>

  <?php elseif($status === 'upcoming'): ?>
    <div class="glass-card text-center">
      <h3>This election has not started yet</h3>
      <p>It will begin on <strong><?= e($e['start_datetime']) ?></strong></p>
    </div>

  <?php else: // past ?>
    <div class="glass-card">
      <h3>Election Results</h3>
      <?php if($results): ?>
        <div class="winner-card">
          <?php if($results[0]['photo']): ?>
            <img src="/club_voting/uploads/<?= e($results[0]['photo']) ?>" alt="Winner">
          <?php endif; ?>
          <div>
            <h4>🏆 Winner</h4>
            <p><strong><?= e($results[0]['name']) ?></strong> with <?= e($results[0]['votes']) ?> votes</p>
          </div>
        </div>

        <?php $max_votes = max(array_column($results,'votes')) ?: 1; ?>
        <div class="vote-bars">
          <?php 
          $gradients = ["linear-gradient(90deg,#00d4ff,#6366f1)","linear-gradient(90deg,#ff00ff,#ff63c3)","linear-gradient(90deg,#8b5cf6,#d946ef)","linear-gradient(90deg,#20c997,#38d9a9)","linear-gradient(90deg,#ffc300,#ff6b6b)"];
          $i=0;
          foreach($results as $r): 
            $percentage = round(($r['votes']/$max_votes)*100);
            $color = $gradients[$i % count($gradients)];
          ?>
            <div class="vote-bar">
              <div class="vote-info">
                <span><?= e($r['name']) ?></span>
                <span><?= e($r['votes']) ?> votes</span>
              </div>
              <div class="bar-container">
                <div class="bar-fill" style="background:<?= $color ?>" data-width="<?= $percentage ?>%" data-percent="<?= $percentage ?>"></div>
              </div>
            </div>
          <?php $i++; endforeach; ?>
        </div>
      <?php else: ?>
        <p>No votes were cast in this election.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="dashboard.php" class="btn-votekdu-secondary">⬅ Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
  tsParticles.load("tsparticles", {
    background: { color: "transparent" },
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 }},
      color: { value: ["#00d4ff","#ff00ff","#ffffff"] },
      shape: { type: "circle" },
      opacity: { value: 0.6 },
      size: { value: { min:2,max:6 }},
      links: { enable:true, distance:150, color:"#00d4ff", opacity:0.3, width:1 },
      move: { enable:true, speed:1.5, random:true, outModes:{ default:"out" }}
    },
    interactivity: {
      events: { onHover:{ enable:true, mode:"repulse" }, onClick:{ enable:true, mode:"push" }},
      modes: { repulse:{ distance:100 }, push:{ quantity:3 }}
    },
    detectRetina:true
  });

  // Animate vote bars
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.bar-fill').forEach(bar => {
      const width = bar.getAttribute('data-width');
      setTimeout(() => { bar.style.width = width; }, 100);
    });
  });
</script>
</body>
</html>
