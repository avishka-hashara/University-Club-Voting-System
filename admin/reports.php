<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];
$election_id = intval($_GET['election_id'] ?? 0);

// verify election
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND club_id = ?");
$stmt->execute([$election_id, $club_id]);
$e = $stmt->fetch();
if (!$e) { echo "Not found"; exit; }

// CSV export
if (isset($_GET['export']) && $_GET['export']=='csv') {
    // Create CSV of votes aggregated by faculty and department
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="election_' . $election_id . '_report.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Grouping', 'Group', 'Candidate', 'Votes']);
    // faculty-wise
    $sql = "SELECT u.faculty, c.name as candidate, COUNT(v.id) as votes
            FROM votes v
            JOIN users u ON v.user_id = u.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.election_id = ?
            GROUP BY u.faculty, c.id
            ORDER BY u.faculty, votes DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    while ($row = $stmt->fetch()) {
        fputcsv($out, ['faculty', $row['faculty'] ?: 'Unknown', $row['candidate'], $row['votes']]);
    }
    // department-wise
    $sql = "SELECT u.department, c.name as candidate, COUNT(v.id) as votes
            FROM votes v
            JOIN users u ON v.user_id = u.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.election_id = ?
            GROUP BY u.department, c.id
            ORDER BY u.department, votes DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$election_id]);
    while ($row = $stmt->fetch()) {
        fputcsv($out, ['department', $row['department'] ?: 'Unknown', $row['candidate'], $row['votes']]);
    }
    exit;
}

// Compute totals, winner, and aggregated reports
// Total votes per candidate
$stmt = $pdo->prepare("SELECT c.*, COALESCE(SUM(votes_count.count),0) as votes FROM candidates c LEFT JOIN (SELECT candidate_id, COUNT(*) as count FROM votes WHERE election_id = ? GROUP BY candidate_id) votes_count ON c.id = votes_count.candidate_id WHERE c.election_id = ? GROUP BY c.id ORDER BY votes DESC");
$stmt->execute([$election_id, $election_id]);
$candidates = $stmt->fetchAll();

// winner (highest votes)
$winner = $candidates[0] ?? null;

// faculty-wise counts and percentages
$sql = "SELECT u.faculty, c.id as candidate_id, c.name as candidate, COUNT(v.id) as votes
        FROM votes v
        JOIN users u ON v.user_id = u.id
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.election_id = ?
        GROUP BY u.faculty, c.id
        ORDER BY u.faculty, votes DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$election_id]);
$faculty_rows = $stmt->fetchAll();

// department-wise
$sql = "SELECT u.department, c.id as candidate_id, c.name as candidate, COUNT(v.id) as votes
        FROM votes v
        JOIN users u ON v.user_id = u.id
        JOIN candidates c ON v.candidate_id = c.id
        WHERE v.election_id = ?
        GROUP BY u.department, c.id
        ORDER BY u.department, votes DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$election_id]);
$dept_rows = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reports - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-4">
  <h1>Reports for <?= e($e['title']) ?></h1>
  <p><a class="btn btn-secondary" href="?election_id=<?= e($election_id) ?>&export=csv">Export CSV</a></p>

  <h3>Overall results</h3>
  <table class="table">
    <thead><tr><th>Candidate</th><th>Votes</th></tr></thead>
    <tbody>
      <?php foreach ($candidates as $c): ?>
        <tr><td><?= e($c['name']) ?></td><td><?= e($c['votes']) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4>Winner</h4>
  <?php if ($winner): ?>
    <div class="alert alert-success"><?= e($winner['name']) ?> (<?= e($winner['votes']) ?> votes)</div>
  <?php else: ?>
    <div class="alert alert-info">No votes cast yet.</div>
  <?php endif; ?>

  <h3>Faculty-wise breakdown</h3>
  <?php if ($faculty_rows): ?>
    <?php
    $cur = null;
    foreach ($faculty_rows as $r) {
        if ($cur !== $r['faculty']) {
            if ($cur !== null) echo "</tbody></table>";
            echo "<h5>" . e($r['faculty'] ?: 'Unknown') . "</h5>";
            echo "<table class='table'><thead><tr><th>Candidate</th><th>Votes</th></tr></thead><tbody>";
            $cur = $r['faculty'];
        }
        echo "<tr><td>" . e($r['candidate']) . "</td><td>" . e($r['votes']) . "</td></tr>";
    }
    echo "</tbody></table>";
    ?>
  <?php else: ?>
    <p>No faculty data.</p>
  <?php endif; ?>

  <h3>Department-wise breakdown</h3>
  <?php if ($dept_rows): ?>
    <?php
    $cur = null;
    foreach ($dept_rows as $r) {
        if ($cur !== $r['department']) {
            if ($cur !== null) echo "</tbody></table>";
            echo "<h5>" . e($r['department'] ?: 'Unknown') . "</h5>";
            echo "<table class='table'><thead><tr><th>Candidate</th><th>Votes</th></tr></thead><tbody>";
            $cur = $r['department'];
        }
        echo "<tr><td>" . e($r['candidate']) . "</td><td>" . e($r['votes']) . "</td></tr>";
    }
    echo "</tbody></table>";
    ?>
  <?php else: ?>
    <p>No department data.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>
