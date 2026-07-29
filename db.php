<?php
$host = 'thomas.proxy.rlwy.net'; // Railway-ի Public Host-ը
$port = '16638';                 // Railway-ի Public Port-ը
$db   = 'railway';               // Բազայի անունը
$user = 'root';                  // Օգտատերը
$pass = 'PRgbbhmvEJxNPSxgUUQYrOjzqdxJRwNq'; // Ձեր գաղտնաբառը

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