<?php
require_once __DIR__ . '/../init.php';
require_role('voter');

// List elections categorized
$now = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT * FROM elections ORDER BY start_datetime DESC");
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
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; margin:0; min-height:100vh; color:#fff; background:linear-gradient(135deg,#0a0a0f 0%,#1a1a2e 50%,#16213e 100%); overflow-x:hidden; position:relative; }
    #tsparticles { position: fixed; top:0; left:0; width:100%; height:100%; z-index:0; }
    .dashboard-container { max-width:1100px; margin:auto; padding:2rem 1rem; position:relative; z-index:1; }
    .section-title { font-size:1.8rem; font-weight:700; margin:2rem 0 1rem; background:linear-gradient(90deg,#00d4ff,#6366f1); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .card { background:rgba(255,255,255,0.05); border-radius:15px; padding:1.2rem 1.5rem; margin-bottom:1rem; transition:all 0.3s ease; backdrop-filter:blur(10px); position:relative; }
    .card:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,212,255,0.2); }
    .card h5 { margin:0 0 0.5rem 0; font-size:1.25rem; }
    .btn { display:inline-block; padding:0.6rem 1.3rem; border-radius:10px; text-decoration:none; font-weight:600; margin-top:0.5rem; transition:all 0.3s ease; cursor:pointer; }
    .btn-primary { background:linear-gradient(90deg,#00d4ff,#6366f1); color:#fff; border:none; }
    .btn-primary:hover { background:linear-gradient(90deg,#0099cc,#4f46e5); }
    .btn-outline { border:2px solid #00d4ff; color:#00d4ff; background:transparent; }
    .btn-outline:hover { background:#00d4ff; color:#fff; }
    .status-tag { position:absolute; top:15px; right:15px; padding:0.3rem 0.8rem; border-radius:12px; font-size:0.85rem; font-weight:600; color:#fff; transition:all 0.3s ease; }
    .status-ongoing { background:#28a745; }
    .status-upcoming { background:#ffc107; color:#000; }
    .status-past { background:#6c757d; }
    .status-tag:hover { transform:scale(1.1); }
    #calendar { background:rgba(255,255,255,0.05); border-radius:15px; padding:1rem; margin-top:1.5rem; backdrop-filter:blur(10px); }
    .fc .fc-daygrid-day { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.05); }
  </style>
</head>
<body>
<div id="tsparticles"></div>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="dashboard-container">
  <h1 class="section-title">Voter Dashboard</h1>

  <h3 class="section-title">Ongoing Elections</h3>
  <?php if ($ongoing): foreach ($ongoing as $e): ?>
    <div class="card">
      <span class="status-tag status-ongoing">Ongoing</span>
      <h5><?= e($e['title']) ?></h5>
      <p><?= e($e['description']) ?></p>
      <a class="btn btn-primary" href="/club_voting/voter/election.php?id=<?= e($e['id']) ?>">Vote / View</a>
    </div>
  <?php endforeach; else: ?>
    <p>No ongoing elections.</p>
  <?php endif; ?>

  <h3 class="section-title">Upcoming Elections</h3>
  <?php if ($upcoming): foreach ($upcoming as $e): ?>
    <div class="card">
      <span class="status-tag status-upcoming">Upcoming</span>
      <h5><?= e($e['title']) ?></h5>
      <p>Starts: <?= e($e['start_datetime']) ?></p>
    </div>
  <?php endforeach; else: ?>
    <p>No upcoming elections.</p>
  <?php endif; ?>

  <h3 class="section-title">Past Elections</h3>
  <?php if ($past): foreach ($past as $e): ?>
    <div class="card">
      <span class="status-tag status-past">Past</span>
      <h5><?= e($e['title']) ?></h5>
      <p>Ended: <?= e($e['end_datetime']) ?></p>
      <a class="btn btn-outline" href="/club_voting/voter/election.php?id=<?= e($e['id']) ?>">View Results</a>
    </div>
  <?php endforeach; else: ?>
    <p>No past elections.</p>
  <?php endif; ?>

  <h3 class="section-title">Election Calendar</h3>
  <div id="calendar"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: [
      <?php foreach ($all as $e): ?>
      {
        title: "<?= e($e['title']) ?>",
        start: "<?= e($e['start_datetime']) ?>",
        end: "<?= e($e['end_datetime']) ?>",
        color: "<?= ($now < $e['start_datetime']) ? '#ffc107' : (($now >= $e['start_datetime'] && $now <= $e['end_datetime']) ? '#28a745' : '#6c757d') ?>"
      },
      <?php endforeach; ?>
    ]
  });
  calendar.render();
});

tsParticles.load("tsparticles", {
  background: { color: "transparent" },
  particles: {
    number: { value: 70, density: { enable: true, value_area: 800 }},
    color: { value: ["#00d4ff","#ff00ff","#ffffff"] },
    shape: { type: "circle" },
    opacity: { value: 0.6 },
    size: { value: { min:2, max:5 }},
    links: { enable:true, distance:150, color:"#00d4ff", opacity:0.3, width:1 },
    move: { enable:true, speed:1.5, random:true, outModes:{ default:"out" }}
  },
  interactivity: {
    events: { onHover:{ enable:true, mode:"repulse" }, onClick:{ enable:true, mode:"push" }},
    modes: { repulse:{ distance:100 }, push:{ quantity:3 }}
  },
  detectRetina:true
});
</script>
</body>
</html>
