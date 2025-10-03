<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$election_id = intval($_GET['election_id'] ?? 0);

// verify election
$stmt = $pdo->prepare('SELECT * FROM elections WHERE id = ?');
$stmt->execute([$election_id]);
$e = $stmt->fetch();
if (!$e) { echo 'Not found'; exit; }

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header(sprintf('Content-Disposition: attachment; filename="election_%d_report.csv"', $election_id));
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Grouping', 'Group', 'Candidate', 'Votes']);

    // faculty-wise
    $sql = 'SELECT u.faculty, c.name as candidate, COUNT(v.id) as votes
            FROM votes v
            JOIN users u ON v.user_id = u.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.election_id = ?
            GROUP BY u.faculty, c.id
            ORDER BY u.faculty, votes DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    while ($row = $stmt->fetch()) {
        fputcsv($out, ['faculty', $row['faculty'] ?: 'Unknown', $row['candidate'], $row['votes']]);
    }

    // department-wise
    $sql = 'SELECT u.department, c.name as candidate, COUNT(v.id) as votes
            FROM votes v
            JOIN users u ON v.user_id = u.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.election_id = ?
            GROUP BY u.department, c.id
            ORDER BY u.department, votes DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    while ($row = $stmt->fetch()) {
        fputcsv($out, ['department', $row['department'] ?: 'Unknown', $row['candidate'], $row['votes']]);
    }

    fclose($out);
    exit;
}

// Totals per candidate
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

// faculty-wise
$sql = 'SELECT u.faculty, c.id as candidate_id, c.name as candidate, COUNT(v.id) as votes
        FROM votes v
        JOIN users u ON v.user_id = u.id
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.election_id = ?
        GROUP BY u.faculty, c.id
        ORDER BY u.faculty, votes DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$election_id]);
$faculty_rows = $stmt->fetchAll();

// department-wise
$sql = 'SELECT u.department, c.id as candidate_id, c.name as candidate, COUNT(v.id) as votes
        FROM votes v
        JOIN users u ON v.user_id = u.id
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.election_id = ?
        GROUP BY u.department, c.id
        ORDER BY u.department, votes DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$election_id]);
$dept_rows = $stmt->fetchAll();
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
      margin: 0;
      padding: 0;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }
    #particles-js {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    .section-title {
      font-size: 1.8rem;
      font-weight: 600;
      margin: 1.5rem 0 0.5rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 1.5rem;
      backdrop-filter: blur(15px);
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 300px;
    }
    th, td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      text-align: left;
      color: #fff;
      white-space: nowrap;
    }
    th { font-weight: 600; }
    .alert { border-radius: 10px; padding: 1rem 1.25rem; margin: 1rem 0; }
    .alert-success { background: rgba(40,167,69,0.2); border: 1px solid #28a745; }
    .alert-info { background: rgba(0,212,255,0.2); border: 1px solid #00d4ff; }
    .btn { display: inline-block; padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; cursor: pointer; font-weight: 500; transition: all 0.2s ease; }
    .btn-custom { background: linear-gradient(135deg,#00d4ff,#6366f1); color: #fff; border: none; }
    .btn-custom:hover { opacity: 0.85; }
    .btn-outline-light { border: 1px solid #fff; color: #fff; background: transparent; }
    .btn-outline-light:hover { background: #fff; color: #000; }
    h5 { font-size: 1.2rem; margin: 1rem 0 0.5rem; }
    .mt-3 { margin-top: 1rem; }
    .mt-4 { margin-top: 1.5rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    @media (max-width: 768px) {
      .container { padding: 1rem; }
      .section-title { font-size: 1.5rem; }
      th, td { padding: 0.5rem 0.75rem; }
      .btn { width: 100%; text-align: center; }
      table { font-size: 0.9rem; }
    }
    @media (max-width: 480px) {
      .section-title { font-size: 1.3rem; }
      .card { padding: 1rem; }
    }
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
      <thead><tr><th>Candidate</th><th>Votes</th></tr></thead>
      <tbody>
        <?php foreach ($candidates as $c): ?>
          <tr><td><?= e($c['name']) ?></td><td><?= e($c['votes']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h4 class="section-title">Winner</h4>
  <?php if ($winner): ?>
    <div class="alert alert-success"><?= e($winner['name']) ?> (<?= e($winner['votes']) ?> votes)</div>
  <?php else: ?>
    <div class="alert alert-info">No votes cast yet.</div>
  <?php endif; ?>

  <h3 class="section-title">Faculty-wise Breakdown</h3>
  <?php if ($faculty_rows): ?>
    <?php
    $cur = null;
    foreach ($faculty_rows as $r) {
        if ($cur !== $r['faculty']) {
            if ($cur !== null) echo "</tbody></table></div>";
            echo "<h5 class='mt-3'>" . e($r['faculty'] ?: 'Unknown') . "</h5>";
            echo "<div class='card'><table><thead><tr><th>Candidate</th><th>Votes</th></tr></thead><tbody>";
            $cur = $r['faculty'];
        }
        echo "<tr><td>" . e($r['candidate']) . "</td><td>" . e($r['votes']) . "</td></tr>";
    }
    echo "</tbody></table></div>";
    ?>
  <?php else: ?>
    <p>No faculty data.</p>
  <?php endif; ?>

  <h3 class="section-title">Department-wise Breakdown</h3>
  <?php if ($dept_rows): ?>
    <?php
    $cur = null;
    foreach ($dept_rows as $r) {
        if ($cur !== $r['department']) {
            if ($cur !== null) echo "</tbody></table></div>";
            echo "<h5 class='mt-3'>" . e($r['department'] ?: 'Unknown') . "</h5>";
            echo "<div class='card'><table><thead><tr><th>Candidate</th><th>Votes</th></tr></thead><tbody>";
            $cur = $r['department'];
        }
        echo "<tr><td>" . e($r['candidate']) . "</td><td>" . e($r['votes']) . "</td></tr>";
    }
    echo "</tbody></table></div>";
    ?>
  <?php else: ?>
    <p>No department data.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="btn btn-outline-light mt-4">⬅ Back</a>
</div>

<!-- Particles.js -->
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
