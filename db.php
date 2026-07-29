<?php
// Ավտոմատ ստուգում ենք՝ արդյոք կան Railway-ի միջավայրի փոփոխականներ, 
// հակառակ դեպքում օգտագործում ենք ձեր լոկալ MAMP-ի տվյալները։
$host = getenv('MYSQLHOST') ?: 'localhost';
$db   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'travelgo_db'; // Ձեր բազայի անունը
$user = getenv('MYSQLUSER') ?: 'root';        // Ձեր MySQL օգտանունը
$pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : 'root'; // Ձեր MySQL գաղտնաբառը
$port = getenv('MYSQLPORT') ?: '3306';        // MySQL պորտը

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