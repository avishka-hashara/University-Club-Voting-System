<?php
require_once __DIR__ . '/../init.php';

require_role('admin');

$club_id = current_user()['club_id'];

$election_id = intval($_GET['election_id'] ?? 0);

$cid = intval($_GET['id'] ?? 0);

// --- Fetch candidate details from the database ---
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ? AND election_id = ?");
$stmt->execute([$cid, $election_id]);
$candidate = $stmt->fetch();

if (!$candidate) { 
    echo "Candidate not found"; 
    exit; 
}

$errors = [];

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (!$name) $errors[] = 'Name required';

    $photo_path = $candidate['photo'];

    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['photo'];

        if ($f['error'] !== UPLOAD_ERR_OK) $errors[] = 'Upload error';

        if ($f['size'] > MAX_UPLOAD_SIZE) $errors[] = 'File too large';

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        if (!in_array($mime, ALLOWED_IMAGE_TYPES)) $errors[] = 'Invalid image type';

        if (empty($errors)) {
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $filename = uniqid('cand_', true) . '.' . $ext;
            $dest = UPLOAD_DIR . $filename;

            if (!move_uploaded_file($f['tmp_name'], $dest)) {
                $errors[] = 'Failed to move uploaded file';
            } else {
                $photo_path = $filename; 
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE candidates SET name = ?, bio = ?, photo = ? WHERE id = ? AND election_id = ?");
        $stmt->execute([$name, $bio, $photo_path, $cid, $election_id]);

        // Flash success message and redirect back to manage page
        flash_set('success','Candidate updated');
        header("Location: manage_candidates.php?election_id={$election_id}");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Candidate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color:#fff;
      min-height:100vh;
      overflow-x:hidden;
      padding-top:80px;
    }
    #tsparticles {
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      z-index:0;
      pointer-events:none;
    }
    .container {
      position: relative;
      z-index: 2;
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    h1 {
      text-align:center;
      font-size:2rem;
      margin-bottom:2rem;
      background: linear-gradient(135deg,#ffffff,#00d4ff);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
    }
    form { display:flex; flex-direction:column; gap:1rem; }
    label {
      font-weight:500;
      margin-bottom:0.3rem;
      display:block;
    }
    input[type="text"],
    textarea,
    input[type="file"] {
      width:100%;
      padding:0.8rem;
      border-radius:10px;
      border:1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.1);
      color:#fff;
      font-size:1rem;
    }
    input:focus, textarea:focus {
      outline:none;
      border-color:#00d4ff;
      box-shadow:0 0 10px rgba(0,212,255,0.4);
      background: rgba(255,255,255,0.15);
    }
    textarea { min-height:100px; resize:vertical; }
    .cand-photo {
      height:80px;
      border-radius:10px;
      object-fit:cover;
      display:block;
      margin-bottom:0.5rem;
    }
    .btn-primary, .btn-secondary {
      width:100%;
      padding:0.8rem;
      border:none;
      border-radius:50px;
      cursor:pointer;
      font-weight:600;
      transition:all 0.3s ease;
      font-size:1rem;
    }
    .btn-primary {
      background:linear-gradient(135deg,#00d4ff,#6366f1);
      color:#fff;
    }
    .btn-primary:hover {
      transform:translateY(-2px);
      box-shadow:0 5px 20px rgba(0,212,255,0.4);
    }
    .btn-secondary {
      background:transparent;
      border:1px solid rgba(255,255,255,0.3);
      color:#fff;
    }
    .btn-secondary:hover {
      background:rgba(255,255,255,0.1);
      transform:translateY(-2px);
    }
    .alert {
      padding:0.8rem 1rem;
      border-radius:10px;
      background:rgba(255,0,0,0.2);
      border:1px solid rgba(255,0,0,0.4);
      color:#ff9999;
      margin-bottom:1rem;
      font-size:0.95rem;
    }
    @media (max-width: 600px) {
      .container {
        margin: 1rem;
        padding: 1.5rem;
      }
      h1 { font-size:1.8rem; }
    }
  </style>
</head>
<body>

<div id="tsparticles"></div>

<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1>Edit Candidate</h1>

  <?php if ($errors): ?>
    <div class="alert"><?= e(implode(', ', $errors)) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?> 

    <div>
      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="<?= e($candidate['name']) ?>" required>
    </div>

    <div>
      <label for="bio">Bio</label>
      <textarea id="bio" name="bio"><?= e($candidate['bio']) ?></textarea>
    </div>

    <div>
      <label>Current Photo</label>
      <?php if ($candidate['photo']): ?>
        <img src="/club_voting/uploads/<?= e($candidate['photo']) ?>" alt="Candidate Photo" class="cand-photo">
      <?php else: ?>
        <p>No photo uploaded.</p>
      <?php endif; ?>
    </div>

    <div>
      <label for="photo">Change Photo (optional)</label>
      <input type="file" id="photo" name="photo" accept="image/*">
    </div>

    <button class="btn-primary" type="submit">Save Changes</button>
    <a href="manage_candidates.php?election_id=<?= e($election_id) ?>" class="btn-secondary" style="text-align:center; display:block;">⬅ Back</a>
  </form>
</div>

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
