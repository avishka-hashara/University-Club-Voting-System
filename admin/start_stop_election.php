<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$id = intval($_GET['id'] ?? 0);

// verify election exists
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ?");
$stmt->execute([$id]);
$e = $stmt->fetch();
if (!$e) { echo "Election not found"; exit; }

// toggle active status
$toggle = $pdo->prepare("UPDATE elections SET is_active = NOT is_active WHERE id = ?");
$toggle->execute([$id]);

header('Location: dashboard.php');
exit;
