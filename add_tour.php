<?php
// Ստանում ենք տվյալները POST-ից կամ JSON body-ից
$data = json_decode(file_get_contents("php://input"), true) ?? $_POST;

$title_hy = $data['title_hy'] ?? '';
$title_en = $data['title_en'] ?? '';
$desc_hy  = $data['description_hy'] ?? $data['desc_hy'] ?? '';
$desc_en  = $data['description_en'] ?? $data['desc_en'] ?? '';
$price    = $data['price'] ?? 0;
$image_url= $data['image_url'] ?? '';

// SQL հարցում
$stmt = $pdo->prepare("INSERT INTO tours (title_hy, title_en, description_hy, description_en, price, image_url) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$title_hy, $title_en, $desc_hy, $desc_en, $price, $image_url]);

echo json_encode(["success" => true]);
?>