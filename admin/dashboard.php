<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

// Load elections for this admin's club
$club_id = current_user()['club_id'];
$stmt = $pdo->prepare("SELECT * FROM elections WHERE club_id = ? ORDER BY created_at DESC");
$stmt->execute([$club_id]);
$elections = $stmt->fetchAll();

// get club info
$clubStmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
$clubStmt->execute([$club_id]);
$club = $clubStmt->fetch();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap + Styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #ffffff;
      min-height: 100vh;
      overflow-x: hidden;
    }

    #tsparticles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      pointer-events: none;
    }

    .dashboard-container {
      position: relative;
      z-index: 1;
      margin-top: 2rem;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    h1, h3 {
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .table {
      color: #fff;
    }
    .table thead {
      background: rgba(255, 255, 255, 0.08);
    }
    .table tbody tr {
      background: rgba(255, 255, 255, 0.03);
      transition: all 0.3s ease;
    }
    .table tbody tr:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .btn {
      border-radius: 10px;
    }

    footer {
      margin-top: 3rem;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(20px);
      padding: 1.5rem;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
  </style>
</head>
<body>

<!-- Particle Background -->
<div id="tsparticles"></div>

<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container dashboard-container">
  <h1 class="mb-3">Admin Dashboard</h1>
  <p>Club: <strong><?= e($club['name']) ?></strong></p>
  <a href="create_election.php" class="btn btn-success mb-3">➕ Create Election</a>

  <h3 class="mb-3">Your Elections</h3>
  <div class="table-responsive">
    <table class="table table-borderless align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>Start</th>
          <th>End</th>
          <th>Status</th>
          <th>Active</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($elections as $el): 
          $now = new DateTime();
          $start = new DateTime($el['start_datetime']);
          $end = new DateTime($el['end_datetime']);
          $status = ($now < $start) ? 'Upcoming' : (($now >= $start && $now <= $end) ? 'Ongoing' : 'Past');
        ?>
        <tr>
          <td><?= e($el['title']) ?></td>
          <td><?= e($el['start_datetime']) ?></td>
          <td><?= e($el['end_datetime']) ?></td>
          <td><?= $status ?></td>
          <td><?= $el['is_active'] ? 'Yes' : 'No' ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="manage_candidates.php?election_id=<?= e($el['id']) ?>">Candidates</a>
            <a class="btn btn-sm btn-secondary" href="edit_election.php?id=<?= e($el['id']) ?>">Edit</a>
            <a class="btn btn-sm btn-warning" href="start_stop_election.php?id=<?= e($el['id']) ?>" onclick="return confirm('Toggle start/stop?')">Start/Stop</a>
            <a class="btn btn-sm btn-info" href="reports.php?election_id=<?= e($el['id']) ?>">Reports</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<footer>
  <p>&copy; 2025 SecureVote University Voting System. Admin Panel</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
  tsParticles.load("tsparticles", {
    background: { color: "transparent" },
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 } },
      color: { value: ["#00d4ff", "#ff00ff", "#ffffff"] },
      shape: { type: "circle" },
      opacity: { value: 0.6 },
      size: { value: { min: 2, max: 6 } },
      links: { enable: true, distance: 150, color: "#00d4ff", opacity: 0.3, width: 1 },
      move: { enable: true, speed: 1.2, random: true, outModes: { default: "out" } }
    },
    interactivity: {
      events: { onHover: { enable: true, mode: "repulse" }, onClick: { enable: true, mode: "push" } },
      modes: { repulse: { distance: 100 }, push: { quantity: 3 } }
    },
    detectRetina: true
  });
</script>
</body>
</html>
