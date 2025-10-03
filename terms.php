<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Terms of Service - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="club_voting/assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f, #16213e);
      color: #fff;
      line-height: 1.8;
      padding: 2rem;
      position: relative;
      overflow-x: hidden;
    }
    #tsparticles {
      position: fixed;
      top:0;
      left:0;
      width:100%;
      height:100%;
      z-index:0;
      pointer-events:none;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
      background: rgba(255,255,255,0.03);
      border-radius: 20px;
      padding: 3rem;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.1);
      position: relative;
      z-index: 1;
    }
    h1 {
      text-align: center;
      margin-bottom: 2rem;
      background: linear-gradient(135deg,#00d4ff,#6366f1);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      font-size:2.5rem;
    }
    h2 { color:#00d4ff; margin-top:2rem; }
  </style>
</head>
<body>

<!-- Particle background -->
<div id="tsparticles"></div>

<?php include __DIR__ . '/_nav.php'; ?>

<div class="container">
  <h1>Terms of Service</h1>
  <p>Welcome to <?= e(SITE_NAME) ?>. By accessing or using our platform, you agree to comply with and be bound by the following terms and conditions.</p>

  <h2>1. Acceptance of Terms</h2>
  <p>By registering or using the platform, you acknowledge that you have read, understood, and agreed to these Terms of Service.</p>

  <h2>2. User Responsibilities</h2>
  <p>You agree to use this platform only for lawful purposes. Any attempt to manipulate election results or access unauthorized data is strictly prohibited.</p>

  <h2>3. Account Security</h2>
  <p>You are responsible for maintaining the confidentiality of your login credentials. Notify us immediately of any unauthorized use.</p>

  <h2>4. Service Availability</h2>
  <p>We strive to ensure 99.9% uptime, but we cannot guarantee uninterrupted service due to maintenance or unforeseen issues.</p>

  <h2>5. Modifications</h2>
  <p>We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance.</p>

  <h2>6. Contact</h2>
  <p>For any inquiries, visit our <a href="support.php">Support Page</a>.</p>
</div>

<!-- tsparticles -->
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
tsParticles.load("tsparticles", {
  background: { color: "transparent" },
  particles: {
    number: { value: 80, density: { enable: true, value_area: 800 } },
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
