<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');

    $host    = getenv('MYSQLHOST')     ?: (getenv('DB_HOST')     ?: "localhost");
    $db_user = getenv('MYSQLUSER')     ?: (getenv('DB_USER')     ?: "root");
    $db_pass = getenv('MYSQLPASSWORD') ?: (getenv('DB_PASSWORD') ?: (getenv('MYSQL_PASSWORD') ?: "root"));
    $db_name = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: "travelgo_db");
    $port    = getenv('MYSQLPORT')     ?: (getenv('DB_PORT')     ?: 3306);

    $conn = @new mysqli($host, $db_user, $db_pass, $db_name, (int)$port);
    
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Բազայի միացման սխալ: " . $conn->connect_error]);
        exit;
    }
    $conn->set_charset("utf8mb4");

    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Խնդրում ենք լրացնել բոլոր դաշտերը"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
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

    $stmt->close();
    $conn->close();
    exit;
}

session_start();
?>
<!DOCTYPE html>
<html lang="hy">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TravelGo - Մուտք</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Poppins:wght@300;400;600&display=swap');
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #080d1a; min-height: 100vh; display: flex; justify-content: center; align-items: center; overflow: hidden; perspective: 1000px; }
    .animated-bg { position: absolute; width: 100%; height: 100%; z-index: 1; }
    .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6; animation: pulse 8s ease-in-out infinite alternate; }
    .blob-1 { width: 400px; height: 400px; background: #00f2fe; top: -100px; left: -100px; }
    .blob-2 { width: 450px; height: 450px; background: #4facfe; bottom: -150px; right: -100px; animation-delay: -3s; }
    .blob-3 { width: 300px; height: 300px; background: #ff0844; bottom: 20%; left: 20%; animation-delay: -5s; }
    @keyframes pulse { 0% { transform: scale(1) translate(0, 0); } 100% { transform: scale(1.2) translate(40px, -30px); } }
    .floating-icon { position: absolute; color: rgba(255, 255, 255, 0.15); animation: float 12s linear infinite; }
    .plane-1 { font-size: 4rem; top: 15%; left: 10%; animation-duration: 15s; }
    .compass-1 { font-size: 5rem; bottom: 15%; right: 12%; animation-duration: 20s; }
    .globe-1 { font-size: 6rem; top: 60%; left: 5%; animation-duration: 25s; }
    @keyframes float { 0% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(180deg); } 100% { transform: translateY(0) rotate(360deg); } }
    
    .main-card { 
      position: relative;
      width: 450px; 
      max-width: 95%; 
      background: rgba(255, 255, 255, 0.05); 
      backdrop-filter: blur(25px); 
      -webkit-backdrop-filter: blur(25px); 
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 28px; 
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6); 
      overflow: hidden; 
      z-index: 10;
    }
    .form-container { 
      padding: 50px 40px; 
      display: flex; 
      flex-direction: column;
      justify-content: center; 
      align-items: center; 
    }
    .glass-form { width: 100%; color: #fff; }
    .glow-text { font-family: 'Orbitron', sans-serif; font-size: 2rem; letter-spacing: 1px; text-shadow: 0 0 15px rgba(0, 242, 254, 0.6); margin-bottom: 5px; text-align: center; }
    .subtitle { font-size: 0.85rem; color: rgba(255, 255, 255, 0.6); margin-bottom: 30px; text-align: center; }
    .input-box { position: relative; margin-bottom: 25px; width: 100%; }
    .input-box input { width: 100%; padding: 12px 10px; background: transparent; border: none; border-bottom: 2px solid rgba(255, 255, 255, 0.3); outline: none; color: #fff; font-size: 0.95rem; transition: 0.3s; }
    .input-box label { position: absolute; left: 0; top: 12px; color: rgba(255, 255, 255, 0.6); pointer-events: none; transition: 0.3s ease all; }
    .input-box input:focus ~ label, .input-box input:not(:placeholder-shown) ~ label { top: -12px; font-size: 0.75rem; color: #00f2fe; }
    .options { display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); margin-bottom: 25px; }
    .options a { color: #00f2fe; text-decoration: none; }
    .btn-glow { width: 100%; padding: 14px; background: linear-gradient(90deg, #00f2fe, #4facfe); border: none; border-radius: 30px; color: #000; font-weight: 700; font-size: 0.95rem; cursor: pointer; box-shadow: 0 0 20px rgba(0, 242, 254, 0.4); transition: 0.3s; }
    .btn-glow:hover { box-shadow: 0 0 35px rgba(0, 242, 254, 0.8); transform: translateY(-2px); }
    .switch-link { text-align: center; margin-top: 20px; font-size: 0.85rem; }
    .switch-link a { color: #00f2fe; text-decoration: none; font-weight: 600; }
  </style>
</head>
<body>

  <div class="animated-bg">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <i class="fa-solid fa-plane floating-icon plane-1"></i>
    <i class="fa-solid fa-compass floating-icon compass-1"></i>
    <i class="fa-solid fa-earth-americas floating-icon globe-1"></i>
  </div>

  <div class="main-card">
    <div class="form-container">
      <form class="glass-form" onsubmit="handleLoginSubmit(event)">
        <h1 class="glow-text">Մուտք ✈️</h1>
        <p class="subtitle">Բացահայտիր աշխարհն այսօր</p>

        <div class="input-box">
          <input type="email" id="login-email" required placeholder=" ">
          <label><i class="fa-solid fa-envelope"></i> Էլ. Հասցե</label>
        </div>

        <div class="input-box">
          <input type="password" id="login-pass" required placeholder=" ">
          <label><i class="fa-solid fa-lock"></i> Գաղտնաբառ</label>
        </div>

        <div class="options">
          <label><input type="checkbox"> Հիշել ինձ</label>
          <a href="#">Մոռացե՞լ եք:</a>
        </div>

        <button type="submit" class="btn-glow">ՄՈՒՏՔ ԳՈՐԾԵԼ</button>

        <div class="switch-link">
          <span>Չունե՞ք հաշիվ:</span>
          <a href="Reg.php">Գրանցվել</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    function handleLoginSubmit(e) {
      e.preventDefault();
      const email = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-pass').value.trim();

      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, password: password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          localStorage.setItem('active_user', JSON.stringify(data.user));
          alert("🎉 Մուտքը հաջողվեց:");
          window.location.href = "index.php";
        } else {
          alert("❌ " + data.message);
        }
      })
      .catch(() => alert("❌ Սերվերի սխալ"));
    }
  </script>
</body>
</html>