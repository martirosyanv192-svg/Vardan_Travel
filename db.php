<?php
$host = 'localhost';
$db   = 'travelgo_db'; // Ձեր բազայի անունը
$user = 'root';        // Ձեր MySQL օգտանունը
$pass = 'root';            // Ձեր MySQL գաղտնաբառը (օրինակ՝ 'root' կամ դատարկ)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Բազայի միացման սխալ: ' . $e->getMessage()]);
    exit;
}
?>