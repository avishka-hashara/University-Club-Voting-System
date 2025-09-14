<?php
require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - <?= e(SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Landing page background */
body {
    background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 50%, #16213e 100%);
    color: #0a0854ff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
    padding: 20px;
}

/* Centered content card */
.about-card {
    background: rgba(255,255,255,0.95);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    max-width: 700px;
    width: 100%;
}

h1 {
    text-align: center;
    margin-bottom: 1.5rem;
}
p {
    font-size: 1.1rem;
    line-height: 1.6;
}
</style>
</head>
<body>
<?php include __DIR__ . '/_nav.php'; ?>

<div class="about-card">
  <h1>About</h1>
  <p>This lightweight system allows university clubs to run elections online with role-based access controls, CSV reports, and vote integrity via database constraints.</p>
</div>

</body>
</html>
