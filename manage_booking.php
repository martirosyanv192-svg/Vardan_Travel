<?php
require_once 'db.php';

header('Content-Type: application/json');

// Ստանում ենք GET կամ POST պարամետրերը
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($id > 0 && !empty($status)) {
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['success' => true, 'message' => 'Կարգավիճակը փոխվեց']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Բազայի սխալ: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Անվավեր պարամետրեր']);
}
?>