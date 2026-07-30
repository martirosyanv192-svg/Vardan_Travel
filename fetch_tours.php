<?php
<?php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT * FROM tours ORDER BY id DESC");
    $tours = $stmt->fetchAll();
    echo json_encode($tours, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>