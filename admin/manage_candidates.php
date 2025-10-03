<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$election_id = intval($_GET['election_id'] ?? 0);

// verify election exists
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$election_id]);
$election = $stmt->fetch();
if (!$election) { echo "Election not found."; exit; }

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
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Candidates - <?= e($election['title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg,#0a0a0f 0%,#1a1a2e 50%,#16213e 100%); color:#fff; min-height:100vh; overflow-x:hidden; padding-top:70px; position:relative; }
    #tsparticles { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }
    .container { width:90%; max-width:1000px; margin:0 auto; padding:2rem 0; position:relative; z-index:2; }
    .page-header { text-align:center; font-size:2.5rem; margin:6rem 1rem 2rem; background:linear-gradient(135deg,#fff,#00d4ff); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .card-glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:2rem; margin-bottom:2rem; box-shadow:0 10px 30px rgba(0,212,255,0.1); transition: all 0.3s ease; }
    .card-glass:hover { transform: translateY(-5px); box-shadow:0 20px 40px rgba(0,212,255,0.2); }
    .btn { padding:0.5rem 1rem; border-radius:50px; font-weight:500; text-decoration:none; display:inline-block; transition: all 0.3s ease; margin-right:0.5rem; cursor:pointer; border:none; color:#fff; text-align:center; }
    .btn-primary { background: linear-gradient(135deg,#00d4ff,#6366f1); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow:0 5px 20px rgba(0,212,255,0.4); }
    .btn-success { background:#22c55e; }
    .btn-warning { background:#facc15; color:#000; }
    .btn-outline-light { border:1px solid #fff; background:transparent; color:#fff; }
    .btn-outline-light:hover { background: rgba(255,255,255,0.1); }
    table { width:100%; border-collapse:collapse; background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border-radius:15px; overflow:hidden; }
    th, td { padding:0.8rem 1rem; text-align:left; vertical-align:middle; color:#fff; }
    th { border-bottom:1px solid rgba(255,255,255,0.2); }
    img.cand-photo { height:60px; border-radius:10px; object-fit:cover; }
    .header-actions { text-align:right; margin-bottom:1rem; }
    tbody tr { transition:all 0.3s ease; cursor:pointer; }
    tbody tr:hover { background: rgba(0,212,255,0.1); transform: translateY(-2px); box-shadow:0 5px 15px rgba(0,212,255,0.1); }
    @media (max-width:768px) {
      .page-header { font-size:2rem; margin:5rem 1rem 1rem; }
      table, thead, tbody, th, td, tr { display:block; }
      thead tr { display:none; }
      tbody tr { margin-bottom:1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:1rem; }
      td { padding-left:50%; position:relative; text-align:left; }
      td::before { position:absolute; left:1rem; top:0.8rem; white-space:nowrap; font-weight:600; }
      td:nth-of-type(1)::before { content:"Name"; }
      td:nth-of-type(2)::before { content:"Bio"; }
      td:nth-of-type(3)::before { content:"Photo"; }
      td:nth-of-type(4)::before { content:"Actions"; }
      .header-actions { text-align:center; margin-bottom:1.5rem; }
      .btn { width:100%; margin-bottom:0.5rem; }
      img.cand-photo { height:50px; }
    }
  </style>
</head>
<body>

<div id="tsparticles"></div>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1 class="page-header">Candidates for <?= e($election['title']) ?></h1>

  <div class="header-actions">
    <a href="upload_candidate.php?election_id=<?= e($election_id) ?>" class="btn btn-primary">➕ Add Candidate</a>
  </div>

  <div class="card-glass">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Bio</th>
          <th>Photo</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($candidates as $c): ?>
        <tr>
          <td><?= e($c['name']) ?></td>
          <td><?= e($c['bio']) ?></td>
          <td><?php if ($c['photo']): ?><img src="/club_voting/uploads/<?= e($c['photo']) ?>" class="cand-photo"><?php endif; ?></td>
          <td>
            <a class="btn btn-success" href="edit_candidate.php?election_id=<?= e($election_id) ?>&id=<?= e($c['id']) ?>">Edit</a>
            <a class="btn btn-warning" href="manage_candidates.php?election_id=<?= e($election_id) ?>&delete=<?= e($c['id']) ?>&token=<?= e(csrf_token()) ?>" onclick="return confirm('Delete candidate?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($candidates)): ?>
        <tr><td colspan="4" class="text-center">No candidates yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4 header-actions">
    <a href="dashboard.php" class="btn btn-outline-light">⬅ Back to Dashboard</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
tsParticles.load("tsparticles", {
  background: { color: "transparent" },
  particles: {
    number: { value: 50, density: { enable: true, value_area: 800 } },
    color: { value: ["#00d4ff","#ffffff"] },
    shape: { type: "circle" },
    opacity: { value: 0.5 },
    size: { value: { min:2, max:6 } },
    links: { enable: true, distance: 120, color: "#00d4ff", opacity: 0.3, width: 1 },
    move: { enable: true, speed: 1, random: true, outModes: { default: "out" } }
  },
  interactivity: {
    events: { onHover: { enable:true, mode:"repulse" }, onClick: { enable:true, mode:"push" } },
    modes: { repulse: { distance:100 }, push: { quantity:3 } }
  },
  detectRetina:true
});
</script>

</body>
</html>
