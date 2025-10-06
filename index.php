<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= e(SITE_NAME) ?> - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
      overflow-x: hidden;
      min-height: 100vh;
      position: relative;
      line-height:1.6;
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #00d4ff, #6366f1); border-radius: 5px; }
    ::-webkit-scrollbar-thumb:hover { background: #00d4ff; }

    /* Particles */
    #tsparticles {
      position: fixed; top:0; left:0;
      width:100%; height:100%;
      z-index:0; pointer-events:none;
    }

    /* Logo with glow effect */
    .site-logo { 
      text-align:center; 
      margin-top:0; 
      margin-bottom:0; 
      z-index:2; 
      position:relative;
      animation: float 6s ease-in-out infinite;
    }
    .site-logo img { 
      height:300px; 
      user-select:none; 
      -webkit-user-drag:none;
      filter: drop-shadow(0 0 30px rgba(0,212,255,0.3));
      transition: filter 0.3s ease;
    }
    .site-logo img:hover {
      filter: drop-shadow(0 0 50px rgba(0,212,255,0.5));
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    /* Container */
    .container { width:90%; max-width:1200px; margin:0 auto; position:relative; z-index:2; }

    /* Hero Section Enhanced */
    .hero {
      text-align:center;
      padding:0.5rem 1rem 2rem;
      position: relative;
    }
    .hero h1 {
      font-size:clamp(2.5rem, 5vw, 4rem);
      margin-bottom:1.5rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff, #6366f1);
      background-size: 200% 200%;
      animation: gradient 3s ease infinite;
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      font-weight: 800;
      letter-spacing: -1px;
    }
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .hero .tagline {
      font-size:1.3rem;
      margin-bottom:1rem;
      opacity:0.95;
      font-weight: 300;
    }
    .hero .sub-text {
      font-size:1rem;
      margin-bottom:3rem;
      opacity:0.7;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    /* Buttons Enhanced */
    .hero .btn { 
      margin:0.5rem; 
      padding:1rem 2.5rem; 
      border-radius:50px; 
      font-weight:600; 
      text-decoration:none; 
      transition:all 0.3s; 
      display:inline-block;
      font-size: 1.1rem;
      position: relative;
      overflow: hidden;
    }
    .btn-primary { 
      background: linear-gradient(135deg,#00d4ff,#6366f1); 
      color:#fff;
      box-shadow: 0 4px 15px rgba(0,212,255,0.3);
    }
    .btn-primary:hover { 
      background: linear-gradient(135deg,#0099cc,#4f46e5); 
      transform:translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,212,255,0.4);
    }
    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    .btn-primary:hover::before { left: 100%; }
    
    .btn-outline { 
      border:2px solid #00d4ff; 
      color:#00d4ff; 
      background:transparent;
      backdrop-filter: blur(10px);
    }
    .btn-outline:hover { 
      background:#00d4ff; 
      color:#fff;
      transform:translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,212,255,0.4);
    }

    /* Stats Section */
    .stats-section {
      padding: 3rem 0;
      background: rgba(255,255,255,0.02);
      backdrop-filter: blur(20px);
      border-top: 1px solid rgba(255,255,255,0.1);
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin: 2rem 0;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 3rem;
      text-align: center;
    }
    .stat-item h3 {
      font-size: 3rem;
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
      font-weight: 700;
    }
    .stat-item p {
      opacity: 0.8;
      font-size: 1.1rem;
    }

    /* Features Section Enhanced */
    .features-wrapper {
      padding: 3rem 0;
    }
    .section-title {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .section-subtitle {
      text-align: center;
      opacity: 0.7;
      margin-bottom: 4rem;
      font-size: 1.1rem;
    }
    .features {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap:2rem;
      z-index:2; 
      position:relative;
    }
    .feature-card {
      background: rgba(255,255,255,0.03);
      backdrop-filter: blur(20px);
      padding:2.5rem;
      border-radius:24px;
      border:1px solid rgba(255,255,255,0.1);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
    }
    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, #00d4ff, transparent);
      transform: translateX(-100%);
      transition: transform 0.6s;
    }
    .feature-card:hover::before { transform: translateX(100%); }
    .feature-card:hover { 
      transform:translateY(-8px) scale(1.02); 
      box-shadow:0 20px 40px rgba(0,212,255,0.3);
      background: rgba(255,255,255,0.06);
    }
    .feature-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      display: inline-block;
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .feature-card h3 { 
      margin-bottom:0.8rem; 
      font-size:1.4rem;
      font-weight: 600;
    }
    .feature-card p { 
      opacity:0.75;
      line-height: 1.7;
    }

    /* Process Section */
    .process-section {
      padding: 6rem 0;
      background: rgba(0,0,0,0.2);
      position: relative;
    }
    .process-timeline {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      position: relative;
      margin-top: 3rem;
    }
    .process-step {
      text-align: center;
      position: relative;
      padding: 2rem;
    }
    .step-number {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      margin: 0 auto 1.5rem;
      position: relative;
      box-shadow: 0 0 30px rgba(0,212,255,0.4);
    }
    .process-step h3 {
      font-size: 1.3rem;
      margin-bottom: 0.8rem;
    }
    .process-step p {
      opacity: 0.75;
    }
    
    /* CTA Section - New Design */
    .cta-section {
      padding: 5rem 2rem;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      position: relative;
      overflow: hidden;
      margin: 6rem auto;
    }

    

    

    .cta-glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 28px;
      padding: 4rem 3rem;
      max-width: 900px;
      position: relative;
      z-index: 2;
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }

    .cta-badge {
      display: inline-block;
      margin-bottom: 1.5rem;
      padding: 0.6rem 1.8rem;
      border-radius: 50px;
      background: linear-gradient(135deg, rgba(0,212,255,0.2), rgba(99,102,241,0.2));
      color: #00d4ff;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: 1px solid rgba(0,212,255,0.3);
    }

    .cta-section h2 {
      font-size: clamp(2rem, 4vw, 3rem);
      margin-bottom: 1.2rem;
      font-weight: 800;
      background: linear-gradient(135deg, #ffffff, #00d4ff, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: textflow 3s ease infinite;
    }

    @keyframes textflow {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .cta-section p {
      font-size: 1.2rem;
      opacity: 0.85;
      margin-bottom: 2.5rem;
      max-width: 650px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.7;
    }

    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 2.5rem;
    }

    .cta-btn {
      padding: 1rem 2.5rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .cta-btn.primary {
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      color: #fff;
      box-shadow: 0 4px 15px rgba(0,212,255,0.3);
    }

    .cta-btn.primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,212,255,0.4);
    }

    .cta-btn.outline {
      border: 2px solid #00d4ff;
      color: #00d4ff;
      background: transparent;
    }

    .cta-btn.outline:hover {
      background: #00d4ff;
      color: #fff;
      transform: translateY(-3px);
    }

    .cta-highlights {
      display: flex;
      justify-content: center;
      gap: 2.5rem;
      flex-wrap: wrap;
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 2rem;
    }

    .cta-highlight h3 {
      font-size: 1.6rem;
      background: linear-gradient(135deg, #00d4ff, #6366f1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 700;
    }

    .cta-highlight p {
      font-size: 1rem;
      opacity: 0.8;
      margin-top: 0.3rem;
    }

    @media (max-width: 768px) {
      .cta-glass { padding: 3rem 1.5rem; }
      .cta-section h2 { font-size: 2rem; }
      .cta-section p { font-size: 1rem; }
    }


    /* Trust Badges */
    .trust-badges {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 3rem;
      flex-wrap: wrap;
      margin: 4rem 0;
      padding: 3rem;
      background: rgba(255,255,255,0.02);
      border-radius: 20px;
    }
    .badge {
      text-align: center;
      opacity: 0.7;
      transition: all 0.3s;
    }
    .badge:hover {
      opacity: 1;
      transform: scale(1.1);
    }
    .badge-icon {
      font-size: 3rem;
      margin-bottom: 0.5rem;
      filter: grayscale(100%);
      transition: filter 0.3s;
    }
    .badge:hover .badge-icon {
      filter: grayscale(0%);
    }
    .badge p {
      font-size: 0.9rem;
      font-weight: 600;
    }

    /* Floating elements */
    .floating-element {
      position: absolute;
      pointer-events: none;
      opacity: 0.1;
      animation: float-random 20s infinite ease-in-out;
    }
    @keyframes float-random {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      25% { transform: translate(50px, -30px) rotate(90deg); }
      50% { transform: translate(-30px, 50px) rotate(180deg); }
      75% { transform: translate(30px, 30px) rotate(270deg); }
    }

    /* Footer Enhanced */
    .footer {
      text-align:center;
      padding:3rem 1rem;
      font-size:0.9rem;
      border-top:1px solid rgba(255,255,255,0.1);
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(20px);
      z-index:2;
      position:relative;
    }
    .footer-links {
      margin-bottom: 1.5rem;
    }
    .footer-links a {
      color: #00d4ff;
      text-decoration: none;
      margin: 0 1rem;
      transition: color 0.3s;
    }
    .footer-links a:hover {
      color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero h1 { font-size:2.5rem; }
      .hero .tagline { font-size:1.1rem; }
      .feature-card { padding:2rem; }
      .site-logo img { height:180px; }
      .stats-grid { gap: 2rem; }
      .stat-item h3 { font-size: 2.5rem; }
      .trust-badges { gap: 2rem; }
      .cta-section { padding: 4rem 1.5rem; }
      .section-title { font-size: 2rem; }
    }

    /* Loading animation */
    @keyframes pulse {
      0% { opacity: 0.6; }
      50% { opacity: 1; }
      100% { opacity: 0.6; }
    }

    .clubs-marquee {
    overflow: hidden;
    position: relative;
    margin: 4rem 0;
    border-radius: 20px;
    background: rgba(255,255,255,0.02);
    backdrop-filter: blur(20px);
    padding: 1rem 0;
  }

  .marquee-track {
    display: flex;
    width: max-content;
    animation: marquee 20s linear infinite;
  }

  .marquee-group {
    display: flex;
    gap: 3rem;
  }

  .marquee-group img {
    height: 60px;
    object-fit: contain;
    filter: grayscale(100%);
    transition: filter 0.3s, transform 0.3s;
  }

  .marquee-group img:hover {
    filter: grayscale(0%);
    transform: scale(1.1);
  }

  @keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }

  @media (max-width: 768px) {
    .marquee-group img { height: 40px; }
  }


  </style>
</head>
<body>

<!-- Particles background -->
<div id="tsparticles"></div>

<!-- Floating decorative elements -->
<div class="floating-element" style="top: 20%; left: 10%; font-size: 100px;">🗳️</div>
<div class="floating-element" style="top: 60%; right: 10%; font-size: 80px;">📊</div>
<div class="floating-element" style="bottom: 30%; left: 5%; font-size: 90px;">🔒</div>

<!-- Logo -->
<div class="site-logo" data-aos="zoom-in">
  <img src="/club_voting/assets/logo.png" alt="VoteKDU Logo">
</div>

<?php include __DIR__ . '/_nav.php'; ?>

<!-- Hero Section -->
<div class="container hero">
  <h1 data-aos="fade-up">Welcome to VoteKDU</h1>
  <p class="tagline" data-aos="fade-up" data-aos-delay="100">The Future of University Elections is Here</p>
  <p class="sub-text" data-aos="fade-up" data-aos-delay="200">
    Empowering democratic participation through secure, transparent, and efficient digital voting for Kotelawala Defense University clubs and societies.
  </p>
  <div data-aos="fade-up" data-aos-delay="300">
    <?php if (!is_logged_in()): ?>
      <a class="btn btn-primary" href="register.php">Get Started Now</a>
      <a class="btn btn-outline" href="login.php">Sign In</a>
    <?php else: ?>
      <?php if (current_user()['role'] === 'admin'): ?>
        <a class="btn btn-primary" href="/club_voting/admin/dashboard.php">Admin Dashboard</a>
      <?php else: ?>
        <a class="btn btn-primary" href="/club_voting/voter/dashboard.php">Voter Dashboard</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Stats Section -->
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item" data-aos="fade-up">
        <h3>10K+</h3>
        <p>Active Voters</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
        <h3>10+</h3>
        <p>Societies & Clubs</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
        <h3>99.9%</h3>
        <p>Uptime Guarantee</p>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
        <h3>256-bit</h3>
        <p>Encryption Standard</p>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-wrapper">
  <div class="container">
    <h2 class="section-title" data-aos="fade-up">Why Choose VoteKDU?</h2>
    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
      Built with cutting-edge technology to ensure every vote counts
    </p>
    <div class="features">
      <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
        <div class="feature-icon">🔐</div>
        <h3>Military-Grade Security</h3>
        <p>End-to-end encryption with blockchain-inspired audit trails ensures complete vote integrity and prevents tampering.</p>
      </div>
      <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
        <div class="feature-icon">⚡</div>
        <h3>Lightning Fast Results</h3>
        <p>Real-time vote counting with instant result publication once polls close. No more waiting for manual counts.</p>
      </div>
      <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-icon">🎯</div>
        <h3>Multi-Society Support</h3>
        <p>Run simultaneous elections across different clubs with independent management and customizable voting rules.</p>
      </div>
      <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
        <div class="feature-icon">📱</div>
        <h3>Vote Anywhere</h3>
        <p>Fully responsive design works seamlessly on any device. Cast your vote from phone, tablet, or computer.</p>
      </div>
      <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
        <div class="feature-icon">👁️</div>
        <h3>Complete Transparency</h3>
        <p>Anonymous voting with verifiable receipts. Track your vote while maintaining complete privacy.</p>
      </div>
      <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
        <div class="feature-icon">🤝</div>
        <h3>24/7 Support</h3>
        <p>Dedicated support team available round the clock to assist with any issues or questions.</p>
      </div>
    </div>
  </div>
</section>

<!-- How It Works -->
<section class="process-section">
  <div class="container">
    <h2 class="section-title" data-aos="fade-up">How It Works</h2>
    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
      Simple, secure, and straightforward voting process
    </p>
    <div class="process-timeline">
      <div class="process-step" data-aos="fade-up" data-aos-delay="100">
        <div class="step-number">1</div>
        <h3>Register</h3>
        <p>Quick signup with university credentials verification</p>
      </div>
      <div class="process-step" data-aos="fade-up" data-aos-delay="200">
        <div class="step-number">2</div>
        <h3>Verify</h3>
        <p>Secure identity verification ensures one person, one vote</p>
      </div>
      <div class="process-step" data-aos="fade-up" data-aos-delay="300">
        <div class="step-number">3</div>
        <h3>Vote</h3>
        <p>Cast your ballot securely with real-time encryption</p>
      </div>
      <div class="process-step" data-aos="fade-up" data-aos-delay="400">
        <div class="step-number">4</div>
        <h3>Track</h3>
        <p>Monitor results in real-time as votes are counted</p>
      </div>
    </div>
  </div>
</section>

<!-- Trust Badges -->
<div class="container">
  <div class="trust-badges" data-aos="fade-up">
    <div class="badge">
      <div class="badge-icon">🏆</div>
      <p>Award Winning</p>
    </div>
    <div class="badge">
      <div class="badge-icon">🛡️</div>
      <p>ISO Certified</p>
    </div>
    <div class="badge">
      <div class="badge-icon">✅</div>
      <p>GDPR Compliant</p>
    </div>
    <div class="badge">
      <div class="badge-icon">🌍</div>
      <p>Carbon Neutral</p>
    </div>
  </div>
</div>

<!-- University Clubs Marquee (Seamless Infinite Loop) -->
<div class="clubs-marquee" data-aos="fade-up">
  <div class="marquee-track">
    <div class="marquee-group">
      <img src="/club_voting/assets/clubs/club1.png" alt="Club 1">
      <img src="/club_voting/assets/clubs/club2.png" alt="Club 2">
      <img src="/club_voting/assets/clubs/club3.png" alt="Club 3">
      <img src="/club_voting/assets/clubs/club4.png" alt="Club 4">
      <img src="/club_voting/assets/clubs/club5.png" alt="Club 5">
      <img src="/club_voting/assets/clubs/club6.png" alt="Club 6">
    </div>
    <div class="marquee-group">
      <!-- Duplicate logos for seamless loop -->
      <img src="/club_voting/assets/clubs/club1.png" alt="Club 1">
      <img src="/club_voting/assets/clubs/club2.png" alt="Club 2">
      <img src="/club_voting/assets/clubs/club3.png" alt="Club 3">
      <img src="/club_voting/assets/clubs/club4.png" alt="Club 4">
      <img src="/club_voting/assets/clubs/club5.png" alt="Club 5">
      <img src="/club_voting/assets/clubs/club6.png" alt="Club 6">
    </div>
  </div>
</div>

<!-- CTA Section -->
<section class="cta-section" data-aos="zoom-in">
  <div class="cta-glass">
    <span class="cta-badge">Ready to Make a Difference?</span>
    <h2>Your Vote. Your Voice. Your Future.</h2>
    <p>
      Be part of a transparent and secure digital voting experience at KDU. 
      Join thousands of students shaping their clubs’ futures with just one click.
    </p>
    <div class="cta-buttons">
      <?php if (!is_logged_in()): ?>
        <a href="register.php" class="cta-btn primary">Vote Now</a>
      <?php else: ?>
        <?php if (current_user()['role'] === 'admin'): ?>
          <a href="/club_voting/admin/dashboard.php" class="cta-btn primary">Go to Dashboard</a>
        <?php else: ?>
          <a href="/club_voting/voter/dashboard.php" class="cta-btn primary">Go to Dashboard</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <div class="cta-highlights">
      <div class="cta-highlight">
        <h3>💡 10K+</h3>
        <p>Votes Cast</p>
      </div>
      <div class="cta-highlight">
        <h3>🏛️ 10+</h3>
        <p>Active Clubs</p>
      </div>
      <div class="cta-highlight">
        <h3>🔐 100%</h3>
        <p>Secure & Transparent</p>
      </div>
    </div>
  </div>
</section>


<!-- Footer -->
<footer class="footer">
  <div class="footer-links">
    <a href="/club_voting/privacy.php">Privacy Policy</a>
    <a href="/club_voting/terms.php">Terms of Service</a>
    <a href="/club_voting/contact.php">Support</a>
  </div>
  <p>&copy; 2025 VoteKDU - Empowering Democracy in Education</p>
  <p style="opacity: 0.6; font-size: 0.8rem; margin-top: 0.5rem;">
    Trusted by Kotelawala Defense University for secure and transparent elections
  </p>
</footer>

<!-- Scripts -->
<script src="/club_voting/assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  // Initialize AOS
  AOS.init({
    duration: 1000,
    once: true,
    offset: 100
  });

  // Particles configuration
  tsParticles.load("tsparticles", {
    background: { color: "transparent" },
    particles: {
      number: { value: 80, density: { enable: true, value_area: 800 }},
      color: { value: ["#00d4ff", "#ff00ff", "#ffffff"] },
      shape: { type: "circle" },
      opacity: { value: 0.6 },
      size: { value: { min: 2, max: 6 }},
      links: { enable: true, distance: 150, color: "#00d4ff", opacity: 0.3, width: 1 },
      move: { enable: true, speed: 1.5, random: true, outModes: { default: "out" } }
    },
    interactivity: {
      events: { onHover: { enable: true, mode: "repulse" }, onClick: { enable: true, mode: "push" }},
      modes: { repulse: { distance: 100 }, push: { quantity: 4 }}
    },
    detectRetina: true
  });

  // Counter animation for stats
  function animateCounter(el, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = el.dataset.suffix ? 
        Math.floor(current).toLocaleString() + el.dataset.suffix : 
        Math.floor(current).toLocaleString();
    }, 20);
  }

  // Trigger counter animation when stats section is visible
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
        entry.target.classList.add('animated');
        const statNumbers = entry.target.querySelectorAll('.stat-item h3');
        statNumbers.forEach(num => {
          const text = num.textContent;
          if (text.includes('K+')) {
            num.dataset.suffix = 'K+';
            animateCounter(num, 10);
          } else if (text.includes('+')) {
            num.dataset.suffix = '+';
            animateCounter(num, 10);
          } else if (text.includes('%')) {
            num.dataset.suffix = '%';
            num.textContent = '99.9%';
          }
        });
      }
    });
  });

  const statsSection = document.querySelector('.stats-section');
  if (statsSection) observer.observe(statsSection);
</script>

</body>
</html>