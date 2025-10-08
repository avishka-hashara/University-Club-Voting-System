<?php 
require_once __DIR__ . '/../init.php';
require_role('admin');

$election_id = intval($_GET['election_id'] ?? 0);

// verify election exists
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$election_id]);
$election = $stmt->fetch();
if (!$election) { echo "Election not found"; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    if (!$name) $errors[] = 'Name required';

    $photo_path = null;

    // Handle cropped photo from Cropper.js
    if (!empty($_POST['cropped_photo'])) {
        $data = $_POST['cropped_photo'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg','jpeg','png','gif'])) $errors[] = 'Invalid image type';
            $data = base64_decode($data);
            if ($data === false) $errors[] = 'Base64 decode failed';

            $filename = uniqid('cand_', true) . '.' . $type;
            $dest = UPLOAD_DIR . $filename;
            if (file_put_contents($dest, $data) === false) $errors[] = 'Failed to save image';
            else $photo_path = $filename;
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
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Candidate - <?= e($election['title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
  <style>
    body { font-family:'Inter',sans-serif; background: linear-gradient(135deg,#0a0a0f 0%,#1a1a2e 50%,#16213e 100%); color:#fff; min-height:100vh; overflow-x:hidden; }
    .container { position:relative; z-index:1; max-width:600px; margin:2rem auto; padding:0 1rem; }
    .page-header { text-align:center; font-size:2.5rem; margin-bottom:2rem; background:linear-gradient(135deg,#fff,#00d4ff); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .card-glass { background: rgba(255,255,255,0.05); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:2rem; transition:all 0.3s ease; margin-bottom:2rem; }
    label { color:#fff; font-weight:500; display:block; margin-bottom:0.5rem; }
    input[type="text"], textarea, input[type="file"] { width:100%; padding:0.6rem 0.8rem; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.05); color:#fff; margin-bottom:1rem; outline:none; transition: all 0.3s ease; }
    .btn { padding:0.6rem 1rem; border-radius:50px; border:none; color:#fff; cursor:pointer; font-weight:500; }
    .btn-primary { background: linear-gradient(135deg,#00d4ff,#6366f1); width:100%; }
    .btn-outline { border:1px solid #fff; background:transparent; width:100%; margin-top:0.5rem; }
    .btn-outline:hover { background: rgba(255,255,255,0.1); }
    #cropModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); justify-content:center; align-items:center; flex-direction:column; z-index:999; }
    #cropModal img { max-width:80%; max-height:70vh; border-radius:12px; }
    #cropModal .modal-buttons { margin-top:1rem; display:flex; gap:1rem; }
    #finalPreview { display:none; max-width:150px; margin-top:1rem; border-radius:50%; border:2px solid #00d4ff; }
    .alert { background:rgba(255,0,0,0.2); padding:0.5rem 1rem; border-radius:10px; margin-bottom:1rem; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1 class="page-header">Add Candidate</h1>
  <div class="card-glass">
    <h4 style="color:#00d4ff;">Election: <?= e($election['title']) ?></h4>

    <?php if ($errors): ?>
      <div class="alert"><?= e(implode(', ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="candidateForm">
      <?= csrf_field() ?>
      <label>Name</label>
      <input name="name" type="text" required>
      <label>Bio</label>
      <textarea name="bio" rows="3"></textarea>
      <label>Upload Photo</label>
      <input type="file" id="photoInput" accept="image/*" required>
      <img id="finalPreview" alt="Final cropped preview">
      <input type="hidden" name="cropped_photo" id="croppedPhoto">
      <button class="btn btn-primary" type="submit">➕ Add Candidate</button>
      <a href="manage_candidates.php?election_id=<?= e($election_id) ?>" class="btn btn-outline">⬅ Back to Candidates</a>
    </form>
  </div>
</div>

<!-- Crop Modal -->
<div id="cropModal">
  <img id="cropImage">
  <div class="modal-buttons">
    <button id="cropBtn" class="btn btn-primary">✅ Confirm Crop</button>
    <button id="cancelCrop" class="btn btn-outline">❌ Cancel</button>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper;
const photoInput = document.getElementById('photoInput');
const cropModal = document.getElementById('cropModal');
const cropImage = document.getElementById('cropImage');
const cropBtn = document.getElementById('cropBtn');
const cancelCrop = document.getElementById('cancelCrop');
const croppedPhoto = document.getElementById('croppedPhoto');
const finalPreview = document.getElementById('finalPreview');

photoInput.addEventListener('change', (e)=>{
  const file = e.target.files[0];
  if(!file) return;
  const reader = new FileReader();
  reader.onload = function(event){
    cropImage.src = event.target.result;
    cropModal.style.display = 'flex';
    if(cropper) cropper.destroy();
    cropper = new Cropper(cropImage, { aspectRatio:1, viewMode:2, autoCropArea:1 });
  }
  reader.readAsDataURL(file);
});

// Confirm crop
cropBtn.addEventListener('click', ()=>{
  if(cropper){
    const canvas = cropper.getCroppedCanvas({ width:300, height:300 });
    finalPreview.src = canvas.toDataURL('image/png');
    finalPreview.style.display = 'block';
    croppedPhoto.value = canvas.toDataURL('image/png');
    cropModal.style.display = 'none';
    cropper.destroy();
  }
});

// Cancel crop
cancelCrop.addEventListener('click', ()=>{
  cropModal.style.display = 'none';
  photoInput.value = ''; // Reset file input
  if(cropper) cropper.destroy();
});
</script>
</body>
</html>
