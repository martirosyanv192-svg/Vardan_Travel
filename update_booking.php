<?php
require_once 'db.php'; // Ձեր DB միացման ֆայլը

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$id     = isset($data['id']) ? (int)$data['id'] : 0;
$status = isset($data['status']) ? trim($data['status']) : '';

if ($id <= 0 || empty($status)) {
    echo json_encode(["status" => "error", "message" => "Անվավեր տվյալներ"]);
    exit;
}

try {
    // Թարմացնում ենք status-ը DB-ում
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(["status" => "success", "message" => "Status updated successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>