<?php
// Ստուգում ենք՝ արդյոք կա Railway-ի միջավայրի փոփոխական
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'travelgo_db';
$user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root');

// Եթե URL-ով է տրված (օրինակ DATABASE_URL կամ MYSQL_URL)
$database_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
if ($database_url) {
    $url = parse_url($database_url);
    $host = $url['host'] ?? $host;
    $port = $url['port'] ?? $port;
    $user = $url['user'] ?? $user;
    $pass = $url['pass'] ?? $pass;
    $db   = ltrim($url['path'] ?? '', '/') ?: $db;
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Որպեսզի գաղտնաբառերը կամ սխալները բացահայտ չերևան, բայց դուք հասկանաք խնդիրը
    echo json_encode(['success' => false, 'message' => 'Բազայի միացման սխալ: Ստուգեք Railway-ի Variable-ները']);
    exit;
}
?>