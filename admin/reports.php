<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$election_id = intval($_GET['election_id'] ?? 0);

// verify election
$stmt = $pdo->prepare('SELECT * FROM elections WHERE id = ?');
$stmt->execute([$election_id]);
$e = $stmt->fetch();
if (!$e) { echo 'Not found'; exit; }

// helper: extract faculty from email
function get_faculty_from_email($email) {
    if (preg_match('/\d{2}-(\w+)-\d{2}-\d+@kdu\.ac\.lk/i', $email, $m)) {
        $code = strtoupper($m[1]);
        $map = [
            'BCS' => 'Faculty of Computing',
            'BME' => 'Faculty of Medicine',
            'BEE' => 'Faculty of Engineering',
            'BBA' => 'Faculty of Business',
            // add more mappings if needed
        ];
        return $map[$code] ?? $code;
    }
    return 'Unknown';
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header(sprintf('Content-Disposition: attachment; filename="election_%d_report.csv"', $election_id));
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Candidate', 'Faculty', 'Voter Name', 'Email']);

    $sql = 'SELECT v.id, u.name, u.university_email, c.name as candidate
            FROM votes v
            JOIN users u ON v.user_id = u.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.election_id = ?
            ORDER BY c.id, u.university_email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    while ($row = $stmt->fetch()) {
        $faculty = get_faculty_from_email($row['university_email']);
        fputcsv($out, [$row['candidate'], $faculty, $row['name'], $row['university_email']]);
    }

    fclose($out);
    exit;
}

// candidate totals
$stmt = $pdo->prepare('SELECT c.*, COALESCE(votes_count.count,0) as votes 
    FROM candidates c 
    LEFT JOIN (SELECT candidate_id, COUNT(*) as count 
               FROM votes WHERE election_id = ? 
               GROUP BY candidate_id) votes_count 
    ON c.id = votes_count.candidate_id 
    WHERE c.election_id = ? 
    GROUP BY c.id 
    ORDER BY votes DESC');
$stmt->execute([$election_id, $election_id]);
$candidates = $stmt->fetchAll();

$winner = $candidates[0] ?? null;

// voters detail
$sql = 'SELECT v.id, u.name, u.university_email, c.id as candidate_id, c.name as candidate_name
        FROM votes v
        JOIN users u ON v.user_id = u.id
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.election_id = ?
        ORDER BY c.id, u.university_email';
$stmt = $pdo->prepare($sql);
$stmt->execute([$election_id]);
$voters = $stmt->fetchAll();

// group voters by candidate + faculty
$grouped = [];
foreach ($voters as $v) {
    $faculty = get_faculty_from_email($v['university_email']);
    $grouped[$v['candidate_id']]['name'] = $v['candidate_name'];
    $grouped[$v['candidate_id']]['faculties'][$faculty][] = $v;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reports - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      margin: 0; padding: 0;
      min-height: 100vh; overflow-x: hidden;
    }
    #particles-js { position: fixed; top:0; left:0; width:100%; height:100%; z-index:-1; }
    .container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
    .section-title {
      font-size: 1.8rem; font-weight: 600; margin: 1.5rem 0 0.5rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .card {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 15px; padding: 1.5rem; backdrop-filter: blur(15px);
      margin-bottom: 2rem;
    }
    table { width: 100%; border-collapse: collapse; }
    th, td {
      padding: 0.6rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.1);
      text-align: left; color: #fff;
    }
    th { font-weight: 600; }
    .alert { border-radius: 10px; padding: 1rem 1.25rem; margin: 1rem 0; }
    .alert-success { background: rgba(40,167,69,0.2); border: 1px solid #28a745; }
    .alert-info { background: rgba(0,212,255,0.2); border: 1px solid #00d4ff; }
    .btn { display:inline-block; padding:0.6rem 1.2rem; border-radius:8px;
           text-decoration:none; font-weight:500; transition:0.2s; }
    .btn-custom { background: linear-gradient(135deg,#00d4ff,#6366f1); color:#fff; }
    .btn-custom:hover { opacity:0.85; }
    .btn-outline-light { border:1px solid #fff; color:#fff; background:transparent; }
    .btn-outline-light:hover { background:#fff; color:#000; }
  </style>
</head>
<body>
<div id="particles-js"></div>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1 class="section-title">Reports for <?= e($e['title']) ?></h1>
  <p><a class="btn btn-custom" href="?election_id=<?= e($election_id) ?>&export=csv">⬇ Export CSV</a></p>

  <h3 class="section-title">Overall Results</h3>
  <div class="card">
    <table>
      <thead><tr><th>Candidate</th><th>Total Votes</th></tr></thead>
      <tbody>
        <?php foreach ($candidates as $c): ?>
          <tr><td><?= e($c['name']) ?></td><td><?= e($c['votes']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h4 class="section-title">Winner</h4>
  <?php if ($winner): ?>
    <div class="alert alert-success">🏆 <?= e($winner['name']) ?> (<?= e($winner['votes']) ?> votes)</div>
  <?php else: ?>
    <div class="alert alert-info">No votes cast yet.</div>
  <?php endif; ?>

  <h3 class="section-title">Detailed Voter Breakdown</h3>
  <?php foreach ($grouped as $cid => $cand): ?>
    <h4><?= e($cand['name']) ?></h4>
    <?php foreach ($cand['faculties'] as $faculty => $list): ?>
      <div class="card">
        <h5><?= e($faculty) ?> (<?= count($list) ?>)</h5>
        <table>
          <thead><tr><th>Name</th><th>Email</th></tr></thead>
          <tbody>
            <?php foreach ($list as $v): ?>
              <tr><td><?= e($v['name']) ?></td><td><?= e($v['university_email']) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>

  <a href="dashboard.php" class="btn btn-outline-light mt-4">⬅ Back</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
particlesJS("particles-js", {
  "particles": {
    "number": {"value": 80},
    "size": {"value": 3},
    "move": {"speed": 1},
    "line_linked": {"enable": true, "opacity": 0.3},
    "color": {"value": "#00d4ff"}
  }
});
</script>
</body>
</html>
