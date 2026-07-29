<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)$_POST['id'];
    $title_hy    = $_POST['title_hy'];
    $title_en    = $_POST['title_en'];
    $duration_hy = $_POST['duration_hy'];
    $duration_en = $_POST['duration_en'];
    $price       = $_POST['price'];
    $spots       = $_POST['spots'];
    $tour_date   = $_POST['tour_date'];
    $image_url   = $_POST['image_url'];

    // SQL UPDATE հարցում
    $sql = "UPDATE tours 
            SET title_hy = ?, 
                title_en = ?, 
                duration_hy = ?, 
                duration_en = ?, 
                price = ?, 
                spots = ?, 
                tour_date = ?, 
                image_url = ? 
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    
    $success = $stmt->execute([
        $title_hy,
        $title_en,
        $duration_hy,
        $duration_en,
        $price,
        $spots,
        $tour_date,
        $image_url,
        $id
    ]);

    if ($success) {
        // Հաջողությամբ թարմացվելուց հետո վերադառնում ենք Admin էջ
        header('Location: admin_tours.php?status=updated');
        exit;
    } else {
        echo "Սխալ տեղի ունեցավ թարմացման ընթացքում:";
    }
}
?>