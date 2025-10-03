<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Instructions - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
  /* Global fixes */
  *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #fff;
    min-height: 100vh;
    margin: 0;
    overflow-x: hidden;
    padding-top: 70px; /* space for navbar */
    display: flex;
    flex-direction: column;
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

  .instructions-section {
    max-width: 1100px;
    margin: 2rem auto 3rem;
    padding: 0 1rem;
    position: relative;
    z-index: 2;
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .instructions-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 2.5rem;
    backdrop-filter: blur(15px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    width: 100%;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s forwards;
  }

  .instructions-card h1 {
    background: linear-gradient(135deg, #ffffff, #00d4ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
  }

  .steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap: 1.5rem;
    margin-top: 1rem;
  }

  .step-item {
    background: rgba(255,255,255,0.08);
    padding: 1.5rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  }

  .step-item.show {
    animation: fadeInUp 0.8s forwards;
  }

  .step-item:hover {
    transform: translateY(-8px);
    background: rgba(255,255,255,0.12);
  }

  .step-item h3 {
    font-size: 1.2rem;
    margin-bottom: 0.8rem;
    color: #00d4ff;
  }

  .step-item p {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
  }

  .footer {
    background: rgba(0, 0, 0, 0.3);
    border-top: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    padding: 2rem;
    margin-top: auto;
    color: rgba(255,255,255,0.7);
    position: relative;
    z-index: 2;
  }

  @media (max-width: 600px) {
    .instructions-card { padding: 1.5rem; }
    .instructions-card h1 { font-size: 1.8rem; }
    .step-item h3 { font-size: 1.1rem; }
    .step-item p { font-size: 0.9rem; }
  }
  </style>
</head>
<body>

<div id="tsparticles"></div>
<?php include __DIR__ . '/_nav.php'; ?>

<section class="instructions-section">
  <div class="instructions-card">
    <h1>Step by Step Instructions</h1>
    <div class="steps-grid">
      <div class="step-item">
        <h3>📝 Step 1: Register</h3>
        <p>Create your account using your student details to gain access.</p>
      </div>
      <div class="step-item">
        <h3>🔑 Step 2: Login</h3>
        <p>Login securely with your credentials to enter the voting system.</p>
      </div>
      <div class="step-item">
        <h3>📋 Step 3: View Elections</h3>
        <p>Browse available elections from your club or department.</p>
      </div>
      <div class="step-item">
        <h3>🗳️ Step 4: Cast Your Vote</h3>
        <p>Select your preferred candidates and submit your vote.</p>
      </div>
      <div class="step-item">
        <h3>📊 Step 5: Track Results</h3>
        <p>Stay updated with real-time election results after voting ends.</p>
      </div>
      <div class="step-item">
        <h3>✅ Step 6: Logout</h3>
        <p>Always log out to ensure your account stays safe and secure.</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2025 <?= e(SITE_NAME) ?>. All rights reserved.</p>
</footer>

<script>
  // Fade in step items on scroll
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  document.querySelectorAll('.step-item').forEach(item => observer.observe(item));
</script>

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
