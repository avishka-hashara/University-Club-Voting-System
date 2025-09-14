<?php
require_once __DIR__ . '/../init.php';
require_role('voter');

// List elections categorized
$now = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT e.*, c.name as club_name FROM elections e JOIN clubs c ON e.club_id = c.id ORDER BY e.start_datetime DESC");
$stmt->execute();
$all = $stmt->fetchAll();

$ongoing = $upcoming = $past = [];
foreach ($all as $e) {
    if ($now < $e['start_datetime']) {
        $upcoming[] = $e;
    } elseif ($now >= $e['start_datetime'] && $now <= $e['end_datetime'] && $e['is_active']) {
        $ongoing[] = $e;
    } else {
        $past[] = $e;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Voter Dashboard - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap + FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
    }
    .dashboard-container {
      padding: 2rem;
    }
    .section-title {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      color: #fff;
      backdrop-filter: blur(15px);
    }
    .card h5 { color: #fff; }
    .fc {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 1rem;
    }
    .fc .fc-daygrid-day {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container dashboard-container">
  <h1 class="section-title">Voter Dashboard</h1>

  <!-- Ongoing Elections -->
  <h3 class="section-title">Ongoing Elections</h3>
  <?php if ($ongoing): foreach ($ongoing as $e): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5><?= e($e['title']) ?> <small class="text-muted"> - <?= e($e['club_name']) ?></small></h5>
        <p><?= e($e['description']) ?></p>
        <a class="btn btn-primary" href="/club_voting/voter/election.php?id=<?= e($e['id']) ?>">Vote / View</a>
      </div>
    </div>
  <?php endforeach; else: ?>
    <p>No ongoing elections.</p>
  <?php endif; ?>

  <!-- Upcoming Elections -->
  <h3 class="section-title">Upcoming Elections</h3>
  <?php if ($upcoming): foreach ($upcoming as $e): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5><?= e($e['title']) ?></h5>
        <p>Starts: <?= e($e['start_datetime']) ?></p>
      </div>
    </div>
  <?php endforeach; else: ?>
    <p>No upcoming elections.</p>
  <?php endif; ?>

  <!-- Past Elections -->
  <h3 class="section-title">Past Elections</h3>
  <?php if ($past): foreach ($past as $e): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5><?= e($e['title']) ?></h5>
        <p>Ended: <?= e($e['end_datetime']) ?></p>
        <a class="btn btn-outline-primary" href="/club_voting/voter/election.php?id=<?= e($e['id']) ?>">View Results</a>
      </div>
    </div>
  <?php endforeach; else: ?>
    <p>No past elections.</p>
  <?php endif; ?>

  <!-- Event Calendar -->
  <h3 class="section-title mt-5">Election Calendar</h3>
  <div id="calendar"></div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: [
        <?php foreach ($all as $e): ?>
        {
          title: "<?= e($e['title']) ?> (<?= e($e['club_name']) ?>)",
          start: "<?= e($e['start_datetime']) ?>",
          end: "<?= e($e['end_datetime']) ?>",
          color: "<?= ($now < $e['start_datetime']) ? '#00d4ff' : (($now >= $e['start_datetime'] && $now <= $e['end_datetime']) ? '#28a745' : '#6c757d') ?>"
        },
        <?php endforeach; ?>
      ]
    });
    calendar.render();
  });
</script>
</body>
</html>
