<?php
require_once 'db.php';
$stmt = $pdo->prepare("DELETE FROM bookings");
$stmt->execute();
echo json_encode(['success' => true]);