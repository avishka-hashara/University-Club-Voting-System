<?php
// _nav.php - upgraded navigation partial with animated underline
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="backdrop-filter: blur(15px); background: rgba(0,0,0,0.4);">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4 text-gradient" href="/club_voting/"><?= e(SITE_NAME) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div id="navMain" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/club_voting/">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/club_voting/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/club_voting/contact.php">Contact</a></li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (!is_logged_in()): ?>
          <li class="nav-item">
            <a class="nav-link btn btn-outline-light rounded-pill mx-1 px-3" href="/club_voting/register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link btn btn-primary rounded-pill mx-1 px-3" href="/club_voting/login.php">Login</a>
          </li>
        <?php else: ?>
          <?php $u = current_user(); ?>
          <li class="nav-item">
            <span class="nav-link text-light">Hello, <?= e($u['name']) ?></span>
          </li>
          <?php if ($u['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link btn btn-warning rounded-pill mx-1 px-3 text-dark" href="/club_voting/admin/dashboard.php">Admin</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link btn btn-success rounded-pill mx-1 px-3 text-dark" href="/club_voting/voter/dashboard.php">Voter</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link btn btn-danger rounded-pill mx-1 px-3" href="/club_voting/logout.php">Logout</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
/* Gradient for navbar brand */
.text-gradient {
  background: linear-gradient(90deg, #00d4ff, #6366f1, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Nav links hover animation */
.navbar-nav .nav-link {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

/* Floating underline */
.navbar-nav .nav-link::after {
  content: '';
  position: absolute;
  left: 50%;
  bottom: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #00d4ff, #8b5cf6);
  transition: all 0.3s ease;
  transform: translateX(-50%);
  border-radius: 2px;
}

/* Hover or active effect */
.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
  width: 60%; /* underline expands */
}

/* Buttons hover effect */
.navbar-nav .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,212,255,0.3);
  transition: all 0.3s ease;
}
</style>
