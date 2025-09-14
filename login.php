<?php
require_once __DIR__ . '/init.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) $errors[] = 'Email and password required';
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE university_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'university_email' => $user['university_email'],
                'role' => $user['role'],
                'club_id' => $user['club_id'] ?? null,
                'faculty' => $user['faculty'] ?? null,
                'department' => $user['department'] ?? null,
            ];
            if ($user['role'] === 'admin') {
                header('Location: /club_voting/admin/dashboard.php');
            } else {
                header('Location: /club_voting/voter/dashboard.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - <?= e(SITE_NAME) ?></title>
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
.login-card {
    background: rgba(255,255,255,0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 100%;
    max-width: 400px;
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



<!-- Login form -->
<div class="login-card mt-5">
  <h1>Login</h1>
  <?php if ($errors): ?>
    <div class="alert alert-danger"><ul><?php foreach($errors as $er) echo '<li>' . e($er) . '</li>'; ?></ul></div>
  <?php endif; ?>
  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">University email</label>
      <input required name="email" type="email" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input required name="password" type="password" class="form-control">
    </div>
    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>

</body>
</html>
