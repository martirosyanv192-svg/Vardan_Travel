<?php
// Ստուգում ենք՝ արդյոք կայքն աշխատում է Railway-ում, թե տանը (MAMP)
if (getenv('MYSQLHOST') || getenv('DATABASE_URL')) {
    // Railway-ի միջավայր
    $host = getenv('MYSQLHOST') ?: 'thomas.proxy.rlwy.net';
    $port = getenv('MYSQLPORT') ?: '16638';
    $db   = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'PRgbbhmvEJxNPSxgUUQYrOjzqdxJRwNq';
} else {
    // Լոկալ MAMP միջավայր (համակարգչում)
    $host = 'localhost';
    $port = '3306';
    $db   = 'travelgo_db';
    $user = 'root';
    $pass = 'root';
}

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