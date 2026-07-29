<?php
require_once 'db.php'; // Կամ Ձեր DB միացման ֆայլը

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$tour_id      = (int)($data['tour_id'] ?? 0);
$client_name  = trim($data['client_name'] ?? '');
$client_email = trim($data['client_email'] ?? '');
$status       = 'PENDING'; // 🌟 ՄԻՇՏ ՍՊԱՍՄԱՆ ՄԵՋ

if (empty($client_name) || empty($client_email)) {
    echo json_encode(["status" => "error", "message" => "Լրացրեք բոլոր դաշտերը"]);
    exit;
}

try {
    // ԱՎԵԼԱՑՆՈՒՄ ԵՆՔ STATUS = 'PENDING'
    $stmt = $pdo->prepare("INSERT INTO bookings (tour_id, client_name, client_email, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$tour_id, $client_name, $client_email, $status]);

    echo json_encode(["status" => "success", "message" => "Booking created"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>