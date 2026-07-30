<?php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY id DESC");
    $bookings = $stmt->fetchAll();
    echo json_encode($bookings, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([]);
}