<?php
require_once 'db.php';

// SQL հարցում, որը բերում է նաև տուրի անվանումը ըստ tour_id-ի
$stmt = $pdo->prepare("
    SELECT b.*, t.title_hy AS tour_title 
    FROM bookings b 
    LEFT JOIN tours t ON b.tour_id = t.id 
    ORDER BY b.id DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($bookings);