<?php
require_once __DIR__ . '/../init.php';

require_role('admin');

// Prepare SQL query to fetch all elections, sorted by creation date (newest first)
$stmt = $pdo->prepare("SELECT * FROM elections ORDER BY created_at DESC");
$stmt->execute();

// Fetch all elections from database into an array
$elections = $stmt->fetchAll();

$total = count($elections); 
$now = new DateTime(); 
$upcoming = $ongoing = $past = 0; 

// Loop through each election to determine its status
foreach ($elections as $el) {
    $start = new DateTime($el['start_datetime']);
    $end = new DateTime($el['end_datetime']); 
    
    if ($now < $start) $upcoming++;                  
    elseif ($now >= $start && $now <= $end) $ongoing++; 
    else $past++;                                   
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
      padding-top: 80px;
    }
    #tsparticles { position: fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }

    .dashboard-container {
      position: relative;
      z-index:2;
      margin: 2rem auto;
      max-width: 1200px;
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }

    h1,h3 {
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1rem;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: rgba(255,255,255,0.08);
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      border: 1px solid rgba(255,255,255,0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,212,255,0.4); }
    .stat-card h2 { font-size: 2rem; margin:0; }
    .stat-card p { margin:0; font-size:0.95rem; opacity:0.8; }

    .btn-custom {
      display: inline-block;
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      color: #fff;
      border: none;
      padding: 0.6rem 1rem;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, background 0.3s ease;
      text-decoration: none;
      margin-bottom: 1rem;
    }
    .btn-custom:hover { transform: scale(1.05); background: linear-gradient(135deg, #0099cc, #4f46e5); }

    table { width: 100%; border-collapse: collapse; color: #fff; margin-bottom: 2rem; }
    table thead { background: rgba(255,255,255,0.08); }
    table th, table td { padding: 0.75rem 1rem; text-align: left; }
    table tbody tr { background: rgba(255,255,255,0.03); transition: all 0.3s ease; border-left: 5px solid transparent; }
    table tbody tr:hover { background: rgba(255,255,255,0.08); border-left: 5px solid #00d4ff; }

    .action-btn {
      display: inline-block;
      padding: 0.3rem 0.6rem;
      margin-right: 0.3rem;
      font-size: 0.85rem;
      border-radius: 8px;
      text-decoration: none;
      color: #fff;
      transition: transform 0.2s ease;
    }
    .action-btn-primary { background: #00d4ff; }
    .action-btn-secondary { background: #6366f1; }
    .action-btn-warning { background: #ffcc00; color:#000; }
    .action-btn-info { background: #ff00ff; }
    .action-btn:hover { transform: scale(1.05); }

    footer {
      margin-top: 3rem;
      background: rgba(0,0,0,0.3);
      backdrop-filter: blur(20px);
      padding: 1.5rem;
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.1);
      color: #aaa;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div id="tsparticles"></div>

<?php include __DIR__ . '/../_nav.php'; ?>

<div class="dashboard-container">
  <h1>Admin Dashboard</h1>

  <div class="stats-row">
    <div class="stat-card"><h2><?= $total ?></h2><p>Total Elections</p></div>
    <div class="stat-card"><h2><?= $ongoing ?></h2><p>Ongoing</p></div>
    <div class="stat-card"><h2><?= $upcoming ?></h2><p>Upcoming</p></div>
    <div class="stat-card"><h2><?= $past ?></h2><p>Past</p></div>
  </div>

  <a href="create_election.php" class="btn-custom">➕ Create Election</a>

  <h3>Your Elections</h3>
  <table>
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
      <?php 
      // Loop through each election to display details in table rows
      foreach ($elections as $el): 
        $start = new DateTime($el['start_datetime']);
        $end = new DateTime($el['end_datetime']);
        
        // Determine election status
        $status = ($now < $start) ? 'Upcoming' : (($now >= $start && $now <= $end) ? 'Ongoing' : 'Past');
      ?>
      <tr>
        <td><?= e($el['title']) ?></td>
        <td><?= e($el['start_datetime']) ?></td>
        <td><?= e($el['end_datetime']) ?></td>
        <td><?= $status ?></td>
        <td><?= $el['is_active'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="manage_candidates.php?election_id=<?= e($el['id']) ?>" class="action-btn action-btn-primary">Candidates</a>
          
          <a href="edit_election.php?id=<?= e($el['id']) ?>" class="action-btn action-btn-secondary">Edit</a>
          
          <?php if ($now >= $start && $now <= $end): ?>
            <a href="start_stop_election.php?id=<?= e($el['id']) ?>" class="action-btn action-btn-warning" onclick="return confirm('Toggle start/stop?')">
              <?= $el['is_active'] ? 'Stop' : 'Start' ?>
            </a>
          <?php else: ?>
            <span style="opacity:0.6;">Closed</span>
          <?php endif; ?>
          
          <a href="reports.php?election_id=<?= e($el['id']) ?>" class="action-btn action-btn-info">Reports</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<footer>
  <p>&copy; 2025 SecureVote University Voting System | Admin Panel</p>
</footer>

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
