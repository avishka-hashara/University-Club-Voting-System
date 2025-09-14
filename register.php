<?php
require_once __DIR__ . '/init.php';

// Load clubs for admin dropdown
$stmt = $pdo->query("SELECT id, name FROM clubs ORDER BY name");
$clubs = $stmt->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $role = $_POST['role'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['university_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // validation
    if (!$name) $errors[] = 'Name required';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (UNIVERSITY_EMAIL_DOMAIN && strpos($email, UNIVERSITY_EMAIL_DOMAIN) === false) {
        $errors[] = 'University email must be in domain ' . UNIVERSITY_EMAIL_DOMAIN;
    }
    if (!$password || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $password_confirm) $errors[] = 'Passwords do not match';
    if ($role !== 'admin' && $role !== 'voter') $errors[] = 'Role required';

    if ($role === 'admin') {
        $club_id = intval($_POST['club'] ?? 0);
        $exec_role = trim($_POST['executive_role'] ?? '');
        $nic = trim($_POST['nic_number'] ?? '');
        if (!$club_id) $errors[] = 'Club required for admin';
        if (!$exec_role) $errors[] = 'Executive role required for admin';
        if (!$nic) $errors[] = 'NIC required for admin';
    } else {
        $faculty = trim($_POST['faculty'] ?? '');
        $department = trim($_POST['department'] ?? '');
        if (!$faculty) $errors[] = 'Faculty required';
        if (!$department) $errors[] = 'Department required';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE university_email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered';
        } else {
            $pw_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($role === 'admin') {
                $stmt = $pdo->prepare("INSERT INTO users (name, university_email, role, password, club_id, executive_role, nic_number) VALUES (?, ?, 'admin', ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $pw_hash, $club_id, $exec_role, $nic]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, university_email, role, password, faculty, department) VALUES (?, ?, 'voter', ?, ?, ?)");
                $stmt->execute([$name, $email, $pw_hash, $faculty, $department]);
            }
            flash_set('success', 'Registration successful. You can now login.');
            header('Location: /club_voting/login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - <?= e(SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Same landing page background */
body {
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #0a0854ff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
}

/* Centered card */
.register-card {
    background: rgba(255,255,255,0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 500px;
}


h1 {
    text-align: center;
    margin-bottom: 1.5rem;
}
.alert-danger ul {
    margin: 0;
}
</style>
</head>
<body>
<?php include __DIR__ . '/_nav.php'; ?>


<!-- Register form -->
<div class="register-card mt-5">
  <h1>Register</h1>
  <?php if ($errors): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $er) echo "<li>" . e($er) . "</li>"; ?></ul></div>
  <?php endif; ?>
  <form method="post" id="registerForm">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select class="form-select" name="role" id="roleSelect" required>
        <option value="">Choose role</option>
        <option value="admin">Admin</option>
        <option value="voter">Voter</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Full name</label>
      <input required name="name" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">University email</label>
      <input required name="university_email" type="email" class="form-control">
      <?php if (UNIVERSITY_EMAIL_DOMAIN): ?>
        <div class="form-text">Must contain <?= e(UNIVERSITY_EMAIL_DOMAIN) ?></div>
      <?php endif; ?>
    </div>

    <div id="adminFields" style="display:none;">
      <div class="mb-3">
        <label class="form-label">Club</label>
        <select name="club" class="form-select">
          <option value="">Choose club</option>
          <?php foreach ($clubs as $c): ?>
            <option value="<?= e($c['id']) ?>"><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Executive role (e.g. President)</label>
        <input name="executive_role" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">NIC number</label>
        <input name="nic_number" class="form-control">
      </div>
    </div>

    <div id="voterFields" style="display:none;">
      <div class="mb-3">
        <label class="form-label">Faculty</label>
        <input name="faculty" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Department</label>
        <input name="department" class="form-control">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input required name="password" type="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm password</label>
      <input required name="password_confirm" type="password" class="form-control">
    </div>
    <button class="btn btn-primary w-100">Register</button>
  </form>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function(){
  var r=this.value;
  document.getElementById('adminFields').style.display = r==='admin' ? 'block' : 'none';
  document.getElementById('voterFields').style.display = r==='voter' ? 'block' : 'none';
});
</script>
</body>
</html>
