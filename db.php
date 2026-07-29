<?php
$host = getenv('MYSQLHOST') ?: 'localhost';
$db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'travelgo_db'; 
$user = getenv('MYSQLUSER') ?: 'root';        
$pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : 'root';
$port = getenv('MYSQLPORT') ?: '3306';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Բազայի միացման սխալ: ' . $e->getMessage()]);
    exit;
}
?>