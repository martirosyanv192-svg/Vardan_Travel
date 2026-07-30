<?php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT id, full_name, email, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
    echo json_encode($users, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([]);
}