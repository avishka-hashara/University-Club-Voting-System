<?php
require_once __DIR__ . '/init.php';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $sent = true; // Demo: In a real app, send email
    } else {
        flash_set('error', 'Please fill all fields correctly.');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contact Us - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* Reset & Base */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
      position: relative;
    }

    /* --- Particle container --- */
    #tsparticles {
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      z-index:0;
      pointer-events:none;
    }

    /* Card Container */
    .contact-card {
      position: relative;
      z-index:2;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 2.5rem;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 20px 40px rgba(0, 212, 255, 0.15);
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .page-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .page-header h1 {
      font-size: 2.8rem;
      font-weight: 600;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .page-header p {
      color: rgba(255,255,255,0.8);
      font-size: 1rem;
    }

    /* Form Elements */
    .form-label {
      display: block;
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    input, textarea {
      width: 100%;
      padding: 0.75rem 1rem;
      margin-bottom: 1.2rem;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.2);
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: #00d4ff;
      box-shadow: 0 0 15px rgba(0,212,255,0.4);
      background: rgba(255,255,255,0.15);
    }

    /* Buttons */
    .btn {
      display: inline-block;
      padding: 0.6rem 1.5rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      font-size: 1rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      color: #000;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 212, 255, 0.4);
    }

    /* Alerts */
    .alert {
      border-radius: 12px;
      padding: 1rem;
      margin-bottom: 1rem;
      text-align: center;
      font-weight: 500;
    }

    .alert-success {
      background: rgba(40, 167, 69, 0.2);
      color: #28a745;
      border: 1px solid rgba(40, 167, 69, 0.4);
    }

    .alert-danger {
      background: rgba(220, 53, 69, 0.2);
      color: #dc3545;
      border: 1px solid rgba(220, 53, 69, 0.4);
    }

    /* Responsive */
    @media (max-width: 640px) {
      .contact-card {
        padding: 2rem 1.5rem;
      }

      .page-header h1 {
        font-size: 2rem;
      }
    }

    


  </style>
</head>
<body>

<div id="tsparticles"></div>
<?php include __DIR__ . '/_nav.php'; ?>

<div class="contact-card">
  <div class="page-header">
    <h1>Contact Us</h1>
    <p>We’d love to hear from you. Fill out the form below and we’ll get back to you.</p>
  </div>

  <?php if ($sent): ?>
    <div class="alert alert-success">✅ Message sent. (Demo: not actually emailed.)</div>
  <?php endif; ?>

  <?php if ($err = flash_get('error')): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
  <?php endif; ?>

  <form method="post">
    <?= csrf_field() ?>
    <label class="form-label">Name</label>
    <input required name="name" type="text">

    <label class="form-label">Email</label>
    <input required name="email" type="email">

    <label class="form-label">Message</label>
    <textarea required name="message" rows="5"></textarea>

    <div style="text-align:center;">
      <button type="submit" class="btn btn-primary">Send Message</button>
    </div>
  </form>
</div>



<!-- Particle JS -->
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
