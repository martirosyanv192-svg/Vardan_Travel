<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');

    // Railway DB Connection
    $host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
    $port = getenv('MYSQLPORT') ?: '3306';
    $db   = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'PRgbbhmvEJxNPSxgUUQYrOjzqdxJRwNq';

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Բազայի միացման սխալ"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Խնդրում ենք լրացնել բոլոր դաշտերը"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, full_name, email, password FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            $db_pass = $row['password'];
            $is_pass_correct = false;

            if (
                password_verify($password, $db_pass) || 
                md5($password) === $db_pass || 
                sha1($password) === $db_pass || 
                $password === $db_pass
            ) {
                $is_pass_correct = true;
            }

            if ($is_pass_correct) {
                echo json_encode([
                    "success" => true,
                    "user" => [
                        "id" => $row['id'],
                        "full_name" => $row['full_name'],
                        "email" => $row['email']
                    ]
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Մուտքագրված գաղտնաբառը սխալ է:"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Այս էլ. հասցեով օգտատեր չի գտնվել բազայում:"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Սերվերի սխալ"]);
    }
    exit;
}

session_start();
?>
