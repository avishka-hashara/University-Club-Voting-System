<?php
// Include initialization file (sets up database connection, session, etc.)
require_once __DIR__ . '/../init.php';

// Restrict access - only users with the 'admin' role can access this page
require_role('admin');

// Initialize an empty array to store validation errors
$errors = [];

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token for security
    check_csrf();

    // Get form input values and trim whitespace
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $start = trim($_POST['start_datetime'] ?? '');
    $end = trim($_POST['end_datetime'] ?? '');
    
    // Validate input fields
    if (!$title) $errors[] = 'Title required';
    if (!$start || !$end) $errors[] = 'Start and end times required';
    if (new DateTime($start) >= new DateTime($end)) $errors[] = 'Start must be before end';
    
    // If no validation errors, proceed to insert into database
    if (empty($errors)) {
        // Prepare SQL query to insert election data into the database
        $stmt = $pdo->prepare("INSERT INTO elections (title, description, start_datetime, end_datetime, is_active, created_by) VALUES (?, ?, ?, ?, 0, ?)");
        
        // Execute query with given form data and current user ID
        $stmt->execute([$title, $desc, $start, $end, current_user_id()]);
        
        // Set a success message in session
        flash_set('success', 'Election created');
        
        // Redirect admin back to the dashboard page
        header('Location: /club_voting/admin/dashboard.php');
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create Election - <?= e(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Link to external CSS file -->
  <link href="/club_voting/assets/css/style.css" rel="stylesheet">
  
  <!-- Inline styles for layout and theme -->
  <style>
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%); color:#fff; min-height:100vh; overflow-x:hidden; }
    #tsparticles { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; }
    .form-container { position:relative; z-index:1; margin-top:3rem; background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border-radius:20px; padding:2rem; border:1px solid rgba(255,255,255,0.1); max-width:700px; margin:auto; }
    h1 { background: linear-gradient(135deg,#fff,#00d4ff); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-align:center; margin-bottom:2rem; }
    label { font-weight:500; display:block; margin-bottom:0.5rem; }
    input, textarea { width:100%; padding:0.6rem 0.8rem; border-radius:10px; border:1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.05); color:#fff; margin-bottom:1rem; }
    input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.6); }
    .btn { display:inline-block; padding:0.6rem 1rem; border-radius:10px; font-weight:500; text-decoration:none; transition:all 0.3s ease; margin-right:0.5rem; cursor:pointer; }
    .btn-primary { background:#6366f1; color:#fff; border:none; }
    .btn-primary:hover { background:#4f46e5; }
    .btn-secondary { background:#999; color:#fff; border:none; }
    .alert { background: rgba(255,0,0,0.3); padding:0.8rem; border-radius:10px; margin-bottom:1rem; }
    footer { margin-top:3rem; background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); padding:1.5rem; text-align:center; border-top:1px solid rgba(255,255,255,0.1); }
  </style>
</head>
<body>

<!-- Particle animation background -->
<div id="tsparticles"></div>

<!-- Include navigation bar -->
<?php include __DIR__ . '/../_nav.php'; ?>

<!-- Main form container -->
<div class="form-container">
  <h1>Create Election</h1>

  <!-- Display error messages if validation fails -->
  <?php if ($errors): ?>
    <div class="alert"><?= e(implode(', ', $errors)) ?></div>
  <?php endif; ?>

  <!-- Form for creating a new election -->
  <form method="post">
    <!-- CSRF protection hidden field -->
    <?= csrf_field() ?>

    <!-- Title input -->
    <label>Title</label>
    <input name="title" required placeholder="Enter election title" value="<?= e($_POST['title'] ?? '') ?>">

    <!-- Description input -->
    <label>Description</label>
    <textarea name="description" rows="3" placeholder="Optional description"><?= e($_POST['description'] ?? '') ?></textarea>

    <!-- Start datetime input -->
    <label>Start</label>
    <input name="start_datetime" type="datetime-local" required value="<?= e(date('Y-m-d\TH:i', strtotime($_POST['start_datetime'] ?? '+1 hour'))) ?>">

    <!-- End datetime input -->
    <label>End</label>
    <input name="end_datetime" type="datetime-local" required value="<?= e(date('Y-m-d\TH:i', strtotime($_POST['end_datetime'] ?? '+6 hours'))) ?>">

    <!-- Action buttons -->
    <div style="margin-top:1rem;">
      <button class="btn btn-primary">Create</button>
      <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<!-- Footer section -->
<footer>
  <p>&copy; 2025 SecureVote University Voting System. Admin Panel</p>
</footer>

<!-- Particle animation script -->
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.9.3/tsparticles.bundle.min.js"></script>
<script>
// Initialize and configure the particle background animation
tsParticles.load("tsparticles", {
  background:{color:"transparent"},
  particles:{
    number:{value:60,density:{enable:true,value_area:800}},
    color:{value:["#00d4ff","#ff00ff","#ffffff"]},
    shape:{type:"circle"},
    opacity:{value:0.6},
    size:{value:{min:2,max:6}},
    links:{enable:true,distance:150,color:"#00d4ff",opacity:0.3,width:1},
    move:{enable:true,speed:1.2,random:true,outModes:{default:"out"}}
  },
  interactivity:{
    events:{onHover:{enable:true,mode:"repulse"},onClick:{enable:true,mode:"push"}},
    modes:{repulse:{distance:100},push:{quantity:3}}
  },
  detectRetina:true
});
</script>

</body>
</html>
