<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= e(SITE_NAME) ?> - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Inter' , -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #ffffff;
    overflow-x: hidden;
    min-height: 100vh;
  }

 /* Particles container */
  #tsparticles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
  }

  .section-title {
      text-align: center;
      font-size: 3rem;
      font-weight: 500;
      margin-bottom: 4rem;
      background: linear-gradient(135deg, #ffffff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
  }



  .features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

  .feature-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2.5rem;
    transition: all 0.4s ease;
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
    background: linear-gradient(90deg, #00d4ff, #6366f1, #8b5cf6);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.feature-card:hover::before {
    transform: translateX(0);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 212, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
}

.feature-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #00d4ff, #6366f1);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.feature-card h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #ffffff;
}

.feature-card p {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
}

/* Footer */
.footer {
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 3rem 2rem;
  text-align: center;
}
 

/*bg-particles*/

.bg-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1; /* behind everything */
    overflow: hidden;
}


.particle {
    position: absolute;
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    animation: float linear infinite;
    opacity: 0.8;
    
}

@keyframes float {
    0% {
        transform: translateY(0) scale(1);
        opacity: 0;
    }
    10% {
        opacity: 0.8;
    }
    90% {
        opacity: 0.8;
    }
    100% {
        transform: translateY(-100vh) scale(0.5);
        opacity: 0;
    }
}

.site-logo {
  position: relative;
  display: inline-block;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 0; /* remove padding to prevent clipping */
  background: transparent;
  text-align: center;
  overflow: visible; /* allow halo to extend freely */
}

.site-logo img {
  height: 300px; /* adjust as needed */
  width: auto; /* keep aspect ratio */
  display: block;
  position: relative;
  z-index: 2; /* ensure logo is above halo */
  user-select: none; /* added */
  -webkit-user-drag: none; /* added */
}

.site-logo::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 180px;  /* make halo bigger than logo */
  height: 180px;
  transform: translate(-50%, -50%);
  background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 70%);
  filter: blur(35px); /* very soft edges */
  z-index: 1; /* behind logo */
  pointer-events: none;
}

.site-logo:hover::before {
  background: radial-gradient(circle, rgba(0,212,255,0.3) 0%, rgba(0,212,255,0) 70%);
  filter: blur(40px);
  transform: translate(-50%, -50%) scale(1.05);
}


.societies-marquee {
  background: rgba(255, 255, 255, 0.05); /* thin transparent strip */
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 1rem 0.5rem;
  margin-bottom: 4rem;
}

.societies-marquee .section-title {
  font-size: 2rem;
  font-weight: 500;
  color: #ffffff;
  background: linear-gradient(135deg, #ffffff, #00d4ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 1rem;
}

.marquee-container {
  overflow: hidden;
  position: relative;
  width: 100%;
}

.marquee {
  display: flex;
  gap: 2rem;
  animation: marquee 20s linear infinite; /* infinite loop */
}

.marquee img {
  height: 60px; /* thinner logos */
  width: auto;
  transition: transform 0.3s ease;
}

.marquee img:hover {
  transform: scale(1.05);
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}


</style>


</head>

<body>

<!-- Particles background -->
  <div id="tsparticles"></div>

<!-- Loading screen -->
  <div class="Loader" id="Loader">
    <div class="loader-circle"></div>
  </div>

<!-- Floating Shapes -->
  <div class="floating-shape shape-1"></div>
  <div class="floating-shape shape-2"></div>
  <div class="floating-shape shape-3"></div> 


<!-- Logo -->
  <div class="site-logo">
    <img src="/club_voting/assets/logo.png" alt="VoteKDU Logo">
  </div>


  <?php include __DIR__ . '/_nav.php'; ?>
  <div class="container container-small">
    <div class="py-4 text-center">
      <h1><?= e(SITE_NAME) ?></h1>
      <p class="lead">Secure university club election system — register, vote, and view results.</p>
      <?php if (!is_logged_in()): ?>
        <a class="btn btn-primary" href="register.php">Register</a>
        <a class="btn btn-outline-primary" href="login.php">Login</a>
      <?php else: ?>
        <?php if (current_user()['role'] === 'admin'): ?>
          <a class="btn btn-primary" href="/club_voting/admin/dashboard.php">Admin Dashboard</a>
        <?php else: ?>
          <a class="btn btn-primary" href="/club_voting/voter/dashboard.php">Voter Dashboard</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="row">
      <div class="col-md-8">
        <h3>How it works</h3>
        <p>Admins create elections for their assigned club. Voters see ongoing/upcoming/past elections and can cast one vote per election. Reports include faculty & department breakdowns.</p>
      </div>
      <div class="col-md-4">
        <h5>Quick Links</h5>
        <ul>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact Us</a></li>
        </ul>
      </div>
    </div>
  </div>

<!-- Societies Marquee Section -->
<section class="societies-marquee py-3">
  <div class="container text-center">
    <h2 class="section-title mb-3">Our Societies</h2>
    <div class="marquee-container">
      <div class="marquee">
        <img src="/club_voting/assets/society1.png" alt="Society 1">
        <img src="/club_voting/assets/society2.png" alt="Society 2">
        <img src="/club_voting/assets/society3.png" alt="Society 3">
        <img src="/club_voting/assets/society1.png" alt="Society 1">
        <img src="/club_voting/assets/society2.png" alt="Society 2">
        <img src="/club_voting/assets/society3.png" alt="Society 3">
      </div>
    </div>
  </div>
</section>

</section>




<!-- Features Section -->
  <section class="features" id="features">
    <div class="container">
      <h2 class="section-title">Powerful Features</h2>
      <div class="features-grid">
          <div class="feature-card">
              <div class="feature-icon">🔐</div>
              <h3>End-to-End Security</h3>
              <p>Advanced encryption and blockchain-inspired security measures ensure every vote is protected and tamper-proof.</p>
          </div>
          <div class="feature-card">
                      <div class="feature-icon">⚡</div>
                      <h3>Real-Time Results</h3>
                      <p>Instant vote counting and live result updates with transparent audit trails for complete transparency.</p>
                  </div>
                  <div class="feature-card">
                      <div class="feature-icon">🎯</div>
                      <h3>Multi-Society Support</h3>
                      <p>Simultaneous elections across multiple university societies with independent administration and management.</p>
                  </div>
                  <div class="feature-card">
                      <div class="feature-icon">📱</div>
                      <h3>Mobile-First Design</h3>
                      <p>Optimized for mobile devices with responsive design ensuring seamless voting on any screen size.</p>
                  </div>
                  <div class="feature-card">
                      <div class="feature-icon">🏛️</div>
                      <h3>University Integration</h3>
                      <p>Seamless integration with university ID systems and existing authentication infrastructure.</p>
                  </div>
                  <div class="feature-card">
                      <div class="feature-icon">📊</div>
                      <h3>Advanced Analytics</h3>
                      <p>Comprehensive reporting and analytics with downloadable reports for society records and auditing.</p>
                  </div>
          </div>
      </div>
  </section>



 <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 SecureVote University Voting System. Built for transparency, security, and democracy.</p>
    </div>
  </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/club_voting/assets/js/main.js"></script>


  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>

  <script>
      tsParticles.load("tsparticles", {
        background: {
          color: "transparent"
        },
        particles: {
          number: {
            value: 80,
            density: { enable: true, value_area: 800 }
          },
          color: {
            value: ["#00d4ff", "#ff00ff", "#ffffff"] // neon blue, pink, white
          },
          shape: {
            type: "circle"
          },
          opacity: {
            value: 0.6
          },
          size: {
            value: { min: 2, max: 6 }
          },
          links: {
            enable: true,
            distance: 150,
            color: "#00d4ff",
            opacity: 0.3,
            width: 1
          },
          move: {
            enable: true,
            speed: 1.5,
            direction: "none",
            random: true,
            straight: false,
            outModes: { default: "out" }
          }
        },
        interactivity: {
          events: {
            onHover: { enable: true, mode: "repulse" },
            onClick: { enable: true, mode: "push" }
          },
          modes: {
            repulse: { distance: 100 },
            push: { quantity: 4 }
          }
        },
        detectRetina: true
      });
  </script>

</body>


</html>
