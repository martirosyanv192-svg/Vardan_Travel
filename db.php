<?php
// Ստուգում ենք՝ արդյոք կա Railway-ի DATABASE_URL/MYSQL_URL, թե աշխատում ենք լոկալ (MAMP)
$database_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($database_url) {
    $url = parse_url($database_url);
    $host = $url['host'] ?? 'localhost';
    $port = $url['port'] ?? '3306';
    $user = $url['user'] ?? 'root';
    $pass = $url['pass'] ?? '';
    $db   = ltrim($url['path'] ?? '', '/');
} else {
    // Լոկալ MAMP տվյալներ
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