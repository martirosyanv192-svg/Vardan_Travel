<?php
require_once 'db.php';

// Ստուգում ենք՝ արդյոք ID-ն փոխանցված է
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin_tours.php');
    exit;
}

$id = (int)$_GET['id'];

// Ստանում ենք տվյալ տուրի տվյալները
$stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$id]);
$tour = $stmt->fetch();

if (!$tour) {
    die("Տուրը չի գտնվել:");
}
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <title>Խմբագրել Տուրը</title>
</head>
<body>

<h2>Խմբագրել Տուրը #<?= $tour['id'] ?></h2>

<form action="update_tour.php" method="POST">
    <input type="hidden" name="id" value="<?= $tour['id'] ?>">

    <label>Անվանում (Հայերեն):</label><br>
    <input type="text" name="title_hy" value="<?= htmlspecialchars($tour['title_hy']) ?>" required><br><br>

    <label>Title (English):</label><br>
    <input type="text" name="title_en" value="<?= htmlspecialchars($tour['title_en']) ?>" required><br><br>

    <label>Տևողություն (Հայերեն):</label><br>
    <input type="text" name="duration_hy" value="<?= htmlspecialchars($tour['duration_hy']) ?>"><br><br>

    <label>Duration (English):</label><br>
    <input type="text" name="duration_en" value="<?= htmlspecialchars($tour['duration_en']) ?>"><br><br>

    <label>Գին (AMD):</label><br>
    <input type="number" step="0.01" name="price" value="<?= $tour['price'] ?>" required><br><br>

    <label>Տեղերի քանակ:</label><br>
    <input type="number" name="spots" value="<?= $tour['spots'] ?>" required><br><br>

    <label>Ամսաթիվ / Ժամ:</label><br>
    <input type="datetime-local" name="tour_date" value="<?= date('Y-m-d\TH:i', strtotime($tour['tour_date'])) ?>" required><br><br>

    <label>Նկարի URL:</label><br>
    <input type="text" name="image_url" value="<?= htmlspecialchars($tour['image_url']) ?>"><br><br>

    <button type="submit">Պահպանել Փոփոխությունները</button>
</form>

</body>
</html>