<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>About - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

  <style>
  /* --- Base body --- */
  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #fff;
    min-height: 100vh;
    margin: 0;
    overflow-x: hidden;
    padding-top: 70px; /* leave space for navbar */
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

  /* --- About Section --- */
  .about-section {
    position: relative;
    z-index:2;
    max-width: 1100px;
    margin: 0 auto 60px;
    padding: 0 1rem;
  }

  .about-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 2.5rem;
    backdrop-filter: blur(15px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    animation: fadeInUp 0.8s ease forwards;
  }

  .about-card h1 {
    background: linear-gradient(135deg, #ffffff, #00d4ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 2.2rem;
  }

  .about-card p {
    color: rgba(255,255,255,0.85);
    line-height: 1.7;
    font-size: 1.1rem;
    text-align: center;
    margin-bottom: 2rem;
  }

  /* --- Grid for info items --- */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
  }

  .info-item {
    background: rgba(255,255,255,0.08);
    padding: 1.5rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    opacity: 0;
    transform: translateY(20px);
  }

  .info-item.show {
    animation: fadeInUp 0.8s forwards;
  }

  .info-item:hover {
    transform: translateY(-8px);
    background: rgba(255,255,255,0.12);
  }

  .info-item h3 {
    font-size: 1.3rem;
    margin-bottom: 0.8rem;
    color: #00d4ff;
  }

  .info-item p {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
  }

  /* --- Footer --- */
  .footer {
    position: relative;
    z-index:2;
    background: rgba(0, 0, 0, 0.3);
    border-top: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    padding: 2rem;
    margin-top: 3rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
  }

  /* --- Animations --- */
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* --- Responsive --- */
  @media (max-width: 600px) {
    .about-card { padding: 1.5rem; }
    .about-card h1 { font-size: 1.8rem; }
    .about-card p { font-size: 1rem; }
  }
  </style>
</head>
<body>

<div id="tsparticles"></div>
<?php include __DIR__ . '/_nav.php'; ?>

<section class="about-section">
  <div class="about-card">
    <h1>About VoteKDU</h1>
    <p>
      VoteKDU makes university club elections simple, secure, and stress-free. 
      Say goodbye to paper ballots, long counting sessions, and scheduling conflicts  
      with VoteKDU, your members can vote anytime, anywhere, and see results instantly. 
      Whether you're electing a new president or making important club decisions, our platform 
      ensures every vote counts while keeping the process completely transparent. Built 
      specifically for university clubs, VoteKDU handles everything from member 
      verification to result announcements, so you can focus on what matters most 
       engaging your community and moving your club forward.
    </p>

    <div class="info-grid">
      <div class="info-item">
        <h3>🎯 Our Mission</h3>
        <p>To bring transparency and trust into university club elections by using modern technology.</p>
      </div>
      <div class="info-item">
        <h3>🔒 Security First</h3>
        <p>End-to-end security ensures every vote is private, verified, and tamper-proof.</p>
      </div>
      <div class="info-item">
        <h3>📊 Analytics</h3>
        <p>Generate detailed reports with faculty & department breakdowns for full insights.</p>
      </div>
      <div class="info-item">
        <h3>🤝 Community</h3>
        <p>Built for students, by students — empowering societies to manage elections easily.</p>
      </div>
      <div class="info-item">
        <h3>⚡ Performance</h3>
        <p>Optimized for speed and scalability, ensuring smooth elections even with large numbers of voters.</p>
      </div>
      <div class="info-item">
        <h3>🌍 Accessibility</h3>
        <p>Designed to be inclusive and accessible from any device, anywhere, anytime.</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <p>&copy; 2025 <?= e(SITE_NAME) ?>. All rights reserved.</p>
</footer>

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

<script>
  // Fade in info items on scroll
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  document.querySelectorAll('.info-item').forEach(item => {
    observer.observe(item);
  });
</script>

</body>
</html>
