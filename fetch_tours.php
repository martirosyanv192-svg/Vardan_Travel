<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Ստանում ենք GET պարամետրերը
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 2500000;

    // Հիմնական SQL հարցումը
    $sql = "SELECT * FROM tours WHERE price <= :max_price";

    // Եթե որոնման դաշտում բան է գրված, ավելացնում ենք որոնում ըստ վերնագրի (հայերեն կամ անգլերեն)
    if (!empty($search)) {
        $sql .= " AND (title_hy LIKE :search OR title_en LIKE :search)";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':max_price', $maxPrice);

    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }

    $stmt->execute();
    $tours = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'tours' => $tours
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Սխալ: ' . $e->getMessage(),
        'tours' => []
    ]);
}
?>