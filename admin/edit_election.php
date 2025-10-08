<?php
require_once __DIR__ . '/../init.php';

require_role('admin');

$id = intval($_GET['id'] ?? 0);

// Fetch the election record from the database
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$id]);
$e = $stmt->fetch();

if (!$e) { 
    echo "Election not found."; 
    exit; 
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $start = trim($_POST['start_datetime'] ?? '');
    $end = trim($_POST['end_datetime'] ?? '');

    if (!$title) $errors[] = 'Title required';
    if (!$start || !$end) $errors[] = 'Start and end times required';

    if (empty($errors)) {
        try {
            if (new DateTime($start) >= new DateTime($end)) $errors[] = 'Start must be before end';
        } catch (Exception $ex) {
            $errors[] = 'Invalid date/time format';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE elections 
            SET title = ?, description = ?, start_datetime = ?, end_datetime = ?
            WHERE id = ?");
        $stmt->execute([$title, $desc, $start, $end, $id]);

        flash_set('success','Election updated');
        header('Location: /club_voting/admin/dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Election - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
      overflow-x: hidden;
    }

    #tsparticles {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: 0;
      pointer-events: none;
    }

    .container {
      position: relative;
      z-index: 1;
      max-width: 600px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .page-header {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 2rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .card-glass {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 20px;
      padding: 2rem;
      transition: all 0.3s ease;
      margin-bottom: 2rem;
    }
    .card-glass:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 212, 255, 0.2);
    }

    label { color: #fff; font-weight: 500; display: block; margin-bottom: 0.5rem; }

    input[type="text"], textarea, input[type="datetime-local"] {
      width: 100%;
      padding: 0.6rem 0.8rem;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.05);
      color: #fff;
      margin-bottom: 1rem;
      outline: none;
      transition: all 0.3s ease;
    }
    input:focus, textarea:focus {
      border-color: #00d4ff;
      box-shadow: 0 0 10px rgba(0,212,255,0.4);
    }

    textarea { resize: vertical; min-height: 100px; }

    .btn {
      padding: 0.6rem 1rem;
      border-radius: 50px;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      margin-bottom: 0.5rem;
      cursor: pointer;
      border: none;
      color: #fff;
      text-align: center;
      width: 100%;
    }
    .btn-primary { background: linear-gradient(135deg, #00d4ff, #6366f1); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(0,212,255,0.4); }
    .btn-outline-light { border: 1px solid #fff; background: transparent; }
    .btn-outline-light:hover { background: rgba(255,255,255,0.1); }

    .alert { padding: 0.8rem 1rem; background: rgba(255,0,0,0.2); border-radius: 12px; margin-bottom: 1rem; }

    .footer {
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(20px);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding: 2rem;
      text-align: center;
      margin-top: 3rem;
      color: #fff;
    }

    @media (max-width: 480px) {
      .page-header { font-size: 2rem; margin-bottom: 1.5rem; }
      .card-glass { padding: 1.5rem; }
      input, textarea { padding: 0.5rem; }
    }
  </style>
</head>
<body>

<div id="tsparticles"></div>

<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1 class="page-header">Edit Election</h1>

  <div class="card-glass">
    <?php if ($errors): ?>
      <div class="alert"><?= e(implode(', ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post">
      <?= csrf_field() ?>

      <label>Title</label>
      <input name="title" type="text" value="<?= e($e['title']) ?>" required>

      <label>Description</label>
      <textarea name="description"><?= e($e['description']) ?></textarea>

      <label>Start</label>
      <input type="datetime-local" name="start_datetime"
             value="<?= e(date('Y-m-d\TH:i', strtotime($e['start_datetime']))) ?>" required>

      <label>End</label>
      <input type="datetime-local" name="end_datetime"
             value="<?= e(date('Y-m-d\TH:i', strtotime($e['end_datetime']))) ?>" required>

      <button class="btn btn-primary">💾 Save Changes</button>
      <a href="/club_voting/admin/dashboard.php" class="btn btn-outline-light">⬅ Back to Dashboard</a>
    </form>
  </div>
</div>

<footer class="footer">
  <p>&copy; 2025 SecureVote University Voting System. Built for transparency, security, and democracy.</p>
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
    move: { enable: true, speed: 1.5, random: true, outModes: { default: "out" } }
  },
  interactivity: {
    events: { onHover: { enable: true, mode: "repulse" }, onClick: { enable: true, mode: "push" } },
    modes: { repulse: { distance: 100 }, push: { quantity: 4 } }
  },
  detectRetina: true
});
</script>

</body>
</html>
