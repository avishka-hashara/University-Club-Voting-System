<?php
// Include initialization file (database connection, session, helper functions, etc.)
require_once __DIR__ . '/../init.php';

// Restrict access: only users with 'admin' role can access this page
require_role('admin');

// ==================== LOAD ALL ELECTIONS ====================
// Prepare SQL query to fetch all elections, sorted by creation date (newest first)
$stmt = $pdo->prepare("SELECT * FROM elections ORDER BY created_at DESC");
$stmt->execute();

// Fetch all elections from database into an array
$elections = $stmt->fetchAll();

// ==================== CALCULATE STATISTICS ====================
$total = count($elections); // total number of elections
$now = new DateTime(); // get current date and time
$upcoming = $ongoing = $past = 0; // counters for election status

// Loop through each election to determine its status
foreach ($elections as $el) {
    $start = new DateTime($el['start_datetime']); // start date
    $end = new DateTime($el['end_datetime']); // end date
    
    // Compare current time with start and end times
    if ($now < $start) $upcoming++;                  // election not yet started
    elseif ($now >= $start && $now <= $end) $ongoing++; // election currently ongoing
    else $past++;                                   // election already ended
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Link to site-wide CSS -->
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <!-- Inline styles for the dashboard layout and UI -->
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
      padding-top: 80px; /* Space for navbar */
    }
    #tsparticles { position: fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }

    /* Dashboard main container */
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

    /* Gradient headings */
    h1,h3 {
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1rem;
    }

    /* Stats row grid */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    /* Individual stat card */
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

    /* Create Election button styling */
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

    /* Elections table */
    table { width: 100%; border-collapse: collapse; color: #fff; margin-bottom: 2rem; }
    table thead { background: rgba(255,255,255,0.08); }
    table th, table td { padding: 0.75rem 1rem; text-align: left; }
    table tbody tr { background: rgba(255,255,255,0.03); transition: all 0.3s ease; border-left: 5px solid transparent; }
    table tbody tr:hover { background: rgba(255,255,255,0.08); border-left: 5px solid #00d4ff; }

    /* Action buttons */
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

    /* Footer styling */
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

<!-- Particle animation background -->
<div id="tsparticles"></div>

<!-- Include navigation bar -->
<?php include __DIR__ . '/../_nav.php'; ?>

<!-- =============== DASHBOARD CONTENT =============== -->
<div class="dashboard-container">
  <h1>Admin Dashboard</h1>

  <!-- Stats overview section -->
  <div class="stats-row">
    <div class="stat-card"><h2><?= $total ?></h2><p>Total Elections</p></div>
    <div class="stat-card"><h2><?= $ongoing ?></h2><p>Ongoing</p></div>
    <div class="stat-card"><h2><?= $upcoming ?></h2><p>Upcoming</p></div>
    <div class="stat-card"><h2><?= $past ?></h2><p>Past</p></div>
  </div>

  <!-- Button to create new election -->
  <a href="create_election.php" class="btn-custom">➕ Create Election</a>

  <!-- Table listing all elections -->
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
          <!-- Action: manage candidates -->
          <a href="manage_candidates.php?election_id=<?= e($el['id']) ?>" class="action-btn action-btn-primary">Candidates</a>
          
          <!-- Action: edit election -->
          <a href="edit_election.php?id=<?= e($el['id']) ?>" class="action-btn action-btn-secondary">Edit</a>
          
          <!-- Action: start/stop election (only when ongoing) -->
          <?php if ($now >= $start && $now <= $end): ?>
            <a href="start_stop_election.php?id=<?= e($el['id']) ?>" class="action-btn action-btn-warning" onclick="return confirm('Toggle start/stop?')">
              <?= $el['is_active'] ? 'Stop' : 'Start' ?>
            </a>
          <?php else: ?>
            <span style="opacity:0.6;">Closed</span>
          <?php endif; ?>
          
          <!-- Action: view reports -->
          <a href="reports.php?election_id=<?= e($el['id']) ?>" class="action-btn action-btn-info">Reports</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Footer -->
<footer>
  <p>&copy; 2025 SecureVote University Voting System | Admin Panel</p>
</footer>

<!-- Particle background animation script -->
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
// Initialize particle animation
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
