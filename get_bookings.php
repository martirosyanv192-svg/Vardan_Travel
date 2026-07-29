<?php
// db.php-ի միացում
require_once 'db.php';

// Վերադարձվող ֆորմատը JSON է
header('Content-Type: application/json');

try {
    // Ստանում ենք բոլոր հայտերը/ամրագրումները՝ միացնելով տուրերի աղյուսակը (LEFT JOIN)
    $sql = "SELECT 
                b.id,
                b.tour_id,
                b.client_name,
                b.client_email,
                b.status,
                b.created_at,
                COALESCE(t.title_hy, 'Հեռացված տուր') AS tour_title
            FROM bookings b
            LEFT JOIN tours t ON b.tour_id = t.id
            ORDER BY b.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll();

    // Վերադարձնում ենք տվյալները JSON ձևաչափով
    echo json_encode([
        'success' => true,
        'bookings' => $bookings
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Սխալ տվյալներ ստանալիս: ' . $e->getMessage(),
        'bookings' => []
    ]);
}
?>