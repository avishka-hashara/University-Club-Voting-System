<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Privacy Policy - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
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
    a { color:#00d4ff; text-decoration:none; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>

<!-- Particle background -->
<div id="tsparticles"></div>

<?php include __DIR__ . '/_nav.php'; ?>

<div class="container">
  <h1>Privacy Policy</h1>
  <p>At <?= e(SITE_NAME) ?>, your privacy is our top priority. We are committed to protecting your personal data and ensuring transparency in how we collect, use, and store information.</p>

  <h2>1. Information We Collect</h2>
  <p>We may collect personal details such as your name, student ID, email address, and club membership details when you register or use our platform.</p>

  <h2>2. How We Use Your Information</h2>
  <p>Your data is used strictly for identity verification, secure voting, and platform improvements. We never sell or share your personal information with third parties.</p>

  <h2>3. Data Security</h2>
  <p>We use 256-bit encryption and follow strict security protocols to safeguard your information from unauthorized access or disclosure.</p>

  <h2>4. Cookies</h2>
  <p>Our site may use cookies to improve functionality and user experience. You can disable cookies via your browser settings.</p>

  <h2>5. Contact Us</h2>
  <p>If you have questions about this Privacy Policy, please reach out via our <a href="support.php">Support Page</a>.</p>
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
