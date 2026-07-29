<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $title_hy       = trim($_POST['title_hy'] ?? '');
    $title_en       = trim($_POST['title_en'] ?? '');
    $duration_hy    = trim($_POST['duration_hy'] ?? '');
    $duration_en    = trim($_POST['duration_en'] ?? '');
    $price          = (float)($_POST['price'] ?? 0);
    $spots          = (int)($_POST['spots'] ?? 20);
    $tour_date      = !empty($_POST['tour_date']) ? $_POST['tour_date'] : null;
    $image_url      = trim($_POST['image_url'] ?? '');
    
    // Նկարագրության և Կոորդինատների ստացում
    $description_hy = trim($_POST['description_hy'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $latitude       = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : 40.1792;
    $longitude      = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : 44.5152;

    try {
        if ($id) {
            // UPDATE
            $sql = "UPDATE tours 
                    SET title_hy = ?, 
                        title_en = ?, 
                        duration_hy = ?, 
                        duration_en = ?, 
                        price = ?, 
                        spots = ?, 
                        tour_date = ?, 
                        image_url = ?,
                        description_hy = ?, 
                        description_en = ?,
                        latitude = ?,
                        longitude = ?
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title_hy, $title_en, $duration_hy, $duration_en, 
                $price, $spots, $tour_date, $image_url, 
                $description_hy, $description_en, $latitude, $longitude, $id
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO tours (
                        title_hy, title_en, duration_hy, duration_en, 
                        price, spots, tour_date, image_url, 
                        description_hy, description_en, latitude, longitude
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title_hy, $title_en, $duration_hy, $duration_en, 
                $price, $spots, $tour_date, $image_url, 
                $description_hy, $description_en, $latitude, $longitude
            ]);
        }

        // 🌟 JSON տպելու փոխարեն հետ ենք տանում Admin panel
        header("Location: admin.php?success=1");
        exit;

    } catch (PDOException $e) {
        // Սխալի դեպքում կարող եք հետ ուղարկել սխալի հաղորդագրությամբ
        header("Location: admin.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}
?>