<?php
require_once __DIR__ . '/../init.php';
require_role('admin');

$club_id = current_user()['club_id'];
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM elections WHERE id = ? AND club_id = ?");
$stmt->execute([$id, $club_id]);
$e = $stmt->fetch();
if (!$e) { echo "Not found"; exit; }

$toggle = $pdo->prepare("UPDATE elections SET is_active = NOT is_active WHERE id = ? AND club_id = ?");
$toggle->execute([$id, $club_id]);
header('Location: dashboard.php');
exit;
