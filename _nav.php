<?php
// _nav.php - full-width pure CSS navbar
?>
<nav class="navbar">
  <div class="nav-container">
    <a class="brand text-gradient" href="/club_voting/">VoteKDU</a>
    <button class="toggle" id="navToggle" aria-label="Toggle Menu">&#9776;</button>
    
    <div id="navMain" class="nav-links">
      <ul class="left">
        <li><a href="/club_voting/">Home</a></li>
        <li><a href="/club_voting/about.php">About</a></li>
        <li><a href="/club_voting/contact.php">Contact</a></li>
        <li><a href="/club_voting/instructions.php">Instructions</a></li>
      </ul>

      <ul class="right">
        <?php if (!is_logged_in()): ?>
          <li><a class="btn outline" href="/club_voting/register.php">Register</a></li>
          <li><a class="btn primary" href="/club_voting/login.php">Login</a></li>
        <?php else: ?>
          <?php $u = current_user(); ?>
          <li class="welcome-container"><span class="welcome">Hello, <?= e($u['name']) ?></span></li>
          <?php if ($u['role'] === 'admin'): ?>
            <li><a class="btn warning" href="/club_voting/admin/dashboard.php">Admin</a></li>
          <?php else: ?>
            <li><a class="btn success" href="/club_voting/voter/dashboard.php">Voter</a></li>
          <?php endif; ?>
          <li><a class="btn danger" href="/club_voting/logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
body { padding-top: 70px; }

.navbar {
  position: fixed;
  top: 0; 
  left: 0;
  width: 100%;
  backdrop-filter: blur(15px);
  background: rgba(0,0,0,0.4);
  z-index: 1000;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

/* Full-width container with proper spacing */
.nav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.6rem 1rem; /* Reduced side padding */
  width: 100%;
  max-width: none; /* Remove max-width constraint */
  margin: 0;
  box-sizing: border-box;
}

/* Brand */
.brand {
  font-weight: 700;
  font-size: 1.4rem;
  text-decoration: none;
  margin-right: auto; /* Push brand to the left */
}
.text-gradient {
  background: linear-gradient(90deg, #00d4ff, #6366f1, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Nav links - fixed spacing */
.nav-links { 
  display: flex; 
  align-items: center; 
  justify-content: flex-end; /* Align to the right */
  flex-grow: 1;
  margin-left: 0; /* Remove left margin */
}

.nav-links ul { 
  list-style: none; 
  display: flex; 
  padding: 0; 
  margin: 0; 
  align-items: center;
  gap: 0.5rem; /* Use gap instead of margins for consistent spacing */
}

.nav-links ul.left {
  margin-right: auto; /* Push left links to the left */
}

.nav-links ul.right {
  margin-left: auto; /* Push right links to the right */
}

.nav-links a {
  color: #fff; 
  text-decoration: none; 
  position: relative;
  transition: all 0.3s ease;
  padding: 0.5rem 0.8rem; /* Consistent padding */
  display: block;
  white-space: nowrap;
}
.nav-links a::after {
  content: ''; 
  position: absolute; 
  left: 50%; 
  bottom: -4px; 
  width: 0; 
  height: 2px;
  background: linear-gradient(90deg, #00d4ff, #8b5cf6);
  transition: all 0.3s ease; 
  transform: translateX(-50%);
}
.nav-links a:hover::after, 
.nav-links a.active::after { 
  width: 60%; 
}

/* Buttons */
.btn { 
  display: inline-block; 
  padding: 0.4rem 1rem; 
  border-radius: 20px; 
  font-weight: 500; 
  text-decoration:none; 
  transition: all 0.3s ease; 
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  white-space: nowrap;
}
.btn:hover { 
  transform: translateY(-2px); 
  box-shadow: 0 5px 15px rgba(0,212,255,0.3); 
}
.btn.outline { 
  border: 1px solid #fff; 
  color: #fff; 
  background: transparent;
}
.btn.primary { 
  background: #6366f1; 
  color: #fff; 
}
.btn.success { 
  background: #22c55e; 
  color: #fff; 
}
.btn.warning { 
  background: #facc15; 
  color:#000; 
}
.btn.danger { 
  background: #ef4444; 
  color: #fff; 
}

/* Welcome message */
.welcome-container {
  display: flex;
  align-items: center;
}

.welcome { 
  color: #fff; 
  font-weight: 500;
  font-size: 0.95rem;
  white-space: nowrap;
  display: flex;
  align-items: center;
  padding: 0.5rem 0.8rem;
}

.toggle { 
  display:none; 
  font-size:1.6rem; 
  background:none; 
  border:none; 
  color:#fff; 
  cursor:pointer; 
  margin-left: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-container {
    padding: 0.6rem 1rem;
    flex-wrap: wrap;
  }
  
  .toggle { 
    display: block; 
    order: 2; /* Move toggle to the right */
  }
  
  .brand {
    order: 1; /* Keep brand on the left */
    margin-right: 0;
  }
  
  .nav-links {
    display: none; 
    flex-direction: column; 
    width: 100%;
    order: 3; /* Move nav links below */
    text-align: center; 
    background: rgba(0,0,0,0.9);
    margin-top: 0.5rem; 
    border-radius: 6px; 
    padding: 0.5rem 0;
  }
  
  .nav-links.active { 
    display: flex; 
  }
  
  .nav-links ul { 
    flex-direction: column; 
    width: 100%;
    gap: 0;
  }
  
  .nav-links ul li { 
    margin: 0.3rem 0; 
    width: 100%;
  }
  
  .nav-links a {
    padding: 0.8rem 0;
    width: 100%;
  }
  
  .welcome-container {
    justify-content: center;
  }
  
  .btn {
    width: 90%;
    margin: 0.2rem auto;
    display: block;
  }
}

/* Medium screens adjustment */
@media (min-width: 769px) and (max-width: 1024px) {
  .nav-container {
    padding: 0.6rem 1.5rem;
  }
  
  .nav-links a {
    padding: 0.5rem 0.6rem;
    font-size: 0.9rem;
  }
  
  .btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
  }
}
</style>

<script>
document.getElementById("navToggle").addEventListener("click", () => {
  document.getElementById("navMain").classList.toggle("active");
});

// Close mobile menu when clicking outside
document.addEventListener('click', (event) => {
  const navMain = document.getElementById('navMain');
  const navToggle = document.getElementById('navToggle');
  
  if (navMain.classList.contains('active') && 
      !navMain.contains(event.target) && 
      !navToggle.contains(event.target)) {
    navMain.classList.remove('active');
  }
});
</script>