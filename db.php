<?php
// Ստուգում ենք՝ արդյոք առկա է Railway-ի URL փոփոխականը
$database_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($database_url) {
    // Railway-ի տվյալների ավտոմատ վերծանում
    $url = parse_url($database_url);
    $host = $url['host'] ?? 'localhost';
    $port = $url['port'] ?? '3306';
    $user = $url['user'] ?? 'root';
    $pass = $url['pass'] ?? '';
    $db   = ltrim($url['path'] ?? '', '/');
} else {
    // Լոկալ MAMP տվյալներ (երբ աշխատում եք համակարգչում)
    $host = getenv('MYSQLHOST') ?: 'localhost';
    $port = getenv('MYSQLPORT') ?: '3306';
    $db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'travelgo_db';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : 'root';
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