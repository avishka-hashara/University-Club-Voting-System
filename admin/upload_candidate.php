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
            $type = strtolower($type[1]); // jpg, png, gif

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
    #tsparticles { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }
    .container { position:relative; z-index:1; max-width:600px; margin:2rem auto; padding:0 1rem; }
    .page-header { text-align:center; font-size:2.5rem; margin-bottom:2rem; background:linear-gradient(135deg,#fff,#00d4ff); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .card-glass { background: rgba(255,255,255,0.05); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:2rem; transition:all 0.3s ease; margin-bottom:2rem; }
    .card-glass:hover { transform:translateY(-5px); box-shadow:0 20px 40px rgba(0,212,255,0.2); }
    label { color:#fff; font-weight:500; display:block; margin-bottom:0.5rem; }
    input[type="text"], textarea, input[type="file"] { width:100%; padding:0.6rem 0.8rem; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.05); color:#fff; margin-bottom:1rem; outline:none; transition: all 0.3s ease; }
    input[type="text"]:focus, textarea:focus, input[type="file"]:focus { border-color:#00d4ff; box-shadow:0 0 10px rgba(0,212,255,0.4); }
    .card-glass .btn { width:100%; padding:0.6rem 1rem; border-radius:50px; font-weight:500; text-decoration:none; display:inline-block; transition:all 0.3s ease; margin-bottom:0.5rem; cursor:pointer; border:none; color:#fff; text-align:center; }
    .card-glass .btn-primary { background: linear-gradient(135deg,#00d4ff,#6366f1); }
    .card-glass .btn-primary:hover { transform:translateY(-2px); box-shadow:0 5px 20px rgba(0,212,255,0.4); }
    .card-glass .btn-outline-light { border:1px solid #fff; background:transparent; }
    .card-glass .btn-outline-light:hover { background: rgba(255,255,255,0.1); }
    .alert { padding:0.8rem 1rem; background: rgba(255,0,0,0.2); border-radius:12px; margin-bottom:1rem; }
    .footer { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); border-top:1px solid rgba(255,255,255,0.1); padding:2rem; text-align:center; margin-top:3rem; color:#fff; }
    @media (max-width:480px){ .page-header{font-size:2rem;margin-bottom:1.5rem;} .card-glass{padding:1.5rem;} input[type="text"],textarea,input[type="file"]{padding:0.5rem;} }
  </style>
</head>
<body>

<div id="tsparticles"></div>
<?php include __DIR__ . '/../_nav.php'; ?>

<div class="container">
  <h1 class="page-header">Add Candidate</h1>
  <div class="card-glass">
    <h4 class="mb-4" style="color:#00d4ff;">Election: <?= e($election['title']) ?></h4>

    <?php if ($errors): ?>
      <div class="alert"><?= e(implode(', ', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <label>Name</label>
      <input name="name" type="text" required>
      <label>Bio</label>
      <textarea name="bio" rows="3"></textarea>
      <label>Photo (optional, max 2MB)</label>
      <input type="file" id="photoInput" accept="image/*">
      <img id="cropPreview" style="max-width:100%; display:none; margin-top:1rem;">
      <input type="hidden" name="cropped_photo" id="croppedPhoto">
      <button class="btn btn-primary">➕ Add Candidate</button>
      <a href="manage_candidates.php?election_id=<?= e($election_id) ?>" class="btn btn-outline-light">⬅ Back to Candidates</a>
    </form>
  </div>
</div>

<footer class="footer">
  <p>&copy; 2025 SecureVote University Voting System. Built for transparency, security, and democracy.</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
tsParticles.load("tsparticles", {
  background: { color:"transparent" },
  particles: { number:{value:60,density:{enable:true,value_area:800}}, color:{value:["#00d4ff","#ff00ff","#ffffff"]}, shape:{type:"circle"}, opacity:{value:0.6}, size:{value:{min:2,max:6}}, links:{enable:true,distance:150,color:"#00d4ff",opacity:0.3,width:1}, move:{enable:true,speed:1.5,random:true,outModes:{default:"out"}} },
  interactivity: { events:{ onHover:{enable:true,mode:"repulse"}, onClick:{enable:true,mode:"push"} }, modes:{ repulse:{distance:100}, push:{quantity:4} } },
  detectRetina:true
});

// Cropper.js
let cropper;
const photoInput = document.getElementById('photoInput');
const cropPreview = document.getElementById('cropPreview');
const croppedPhoto = document.getElementById('croppedPhoto');

photoInput.addEventListener('change', (e)=>{
    const file = e.target.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = function(event){
        cropPreview.src = event.target.result;
        cropPreview.style.display = 'block';
        if(cropper) cropper.destroy();
        cropper = new Cropper(cropPreview, { aspectRatio:1, viewMode:1, movable:true, zoomable:true, rotatable:false, scalable:false });
    }
    reader.readAsDataURL(file);
});

document.querySelector('form').addEventListener('submit', function(e){
    if(cropper){
        const canvas = cropper.getCroppedCanvas({ width:300, height:300 });
        croppedPhoto.value = canvas.toDataURL('image/png');
    }
});
</script>
</body>
</html>
