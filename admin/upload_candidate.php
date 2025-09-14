<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];
$election_id = intval($_GET['election_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND club_id = ?");
$stmt->execute([$election_id, $club_id]);
$election = $stmt->fetch();
if (!$election) { echo "Election not found"; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    if (!$name) $errors[] = 'Name required';

    $photo_path = null;
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
        $stmt = $pdo->prepare("INSERT INTO candidates (election_id, name, bio, photo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$election_id, $name, $bio, $photo_path]);
        flash_set('success','Candidate added');
        header("Location: manage_candidates.php?election_id={$election_id}");
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Candidate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>
<div class="container py-4">
  <h1>Add Candidate to <?= e($election['title']) ?></h1>
  <?php if ($errors): ?><div class="alert alert-danger"><?= e(implode(', ', $errors)) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label>Name</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Bio</label>
      <textarea name="bio" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label>Photo (optional, max 2MB)</label>
      <input type="file" name="photo" accept="image/*" class="form-control">
    </div>
    <button class="btn btn-primary">Add</button>
  </form>
</div>
</body>
</html>
