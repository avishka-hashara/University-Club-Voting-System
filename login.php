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

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - <?= e(SITE_NAME) ?></title>
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

    /* Particle container */
    #tsparticles {
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
      z-index:0;
      pointer-events:none;
    }

    /* Card Container */
    .login-card {
      position: relative;
      z-index:2;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 2.5rem;
      max-width: 500px;
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
      font-size: 2.5rem;
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

    input {
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

    input:focus {
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

    @media (max-width: 640px) {
      .login-card {
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

<div class="login-card">
  <div class="page-header">
    <h1>Login</h1>
    <p>Access your account to vote and manage your profile.</p>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul style="list-style:none; padding:0;">
        <?php foreach($errors as $er): ?>
          <li><?= e($er) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($msg = flash_get('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
  <?php endif; ?>

  <form method="post">
    <?= csrf_field() ?>
    <label class="form-label">University Email</label>
    <input required name="email" type="email" placeholder="Enter your university email">

    <label class="form-label">Password</label>
    <input required name="password" type="password" placeholder="Enter your password">

    <div style="text-align:center;">
      <button type="submit" class="btn btn-primary">Login</button>
    </div>
    <div style="text-align:center; margin-top:1rem;">
    <a href="forgot_password.php" style="color:#00d4ff;">Forgot Password?</a>
    </div>

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
