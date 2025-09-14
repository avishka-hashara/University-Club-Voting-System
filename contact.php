<?php
require_once __DIR__ . '/init.php';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $sent = true;
    } else {
        flash_set('error', 'Please fill all fields correctly.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - <?= e(SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Landing page background */
body {
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #0a0854ff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
    padding: 20px;
}

/* Centered content card */
.contact-card {
    background: rgba(255,255,255,0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    max-width: 600px;
    width: 100%;
}

h1 {
    text-align: center;
    margin-bottom: 1.5rem;
}
</style>
</head>
<body>
<?php include __DIR__ . '/_nav.php'; ?>

<div class="contact-card">
  <h1>Contact Us</h1>
  <?php if ($sent): ?>
    <div class="alert alert-success">Message sent. (Demo: not actually emailed.)</div>
  <?php endif; ?>
  <?php if ($err = flash_get('error')): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
  <?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input required name="name" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input required name="email" type="email" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea required name="message" class="form-control"></textarea>
    </div>
    <button class="btn btn-primary w-100">Send</button>
  </form>
</div>

</body>
</html>
