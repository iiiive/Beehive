<?php
session_start();
include("../config.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch admin from database
    $sql = "SELECT db_id, username, password_hash FROM db_access WHERE username = ? LIMIT 1";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['db_logged_in'] = true;
            $_SESSION['db_id'] = $admin['db_id'];
            $_SESSION['username'] = $admin['username'];
            header("Location: database.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }

    if (isset($stmt)) $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HiveCare Admin Database Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: Raleway, sans-serif; }

body {
  height: 100vh; display: flex; align-items: center; justify-content: center;
  position: relative; margin: 0;
}
body::before {
  content: "";
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: url('images/beehive.jpeg') no-repeat center center/cover;
  filter: brightness(20%);
  z-index: -1;
}

.container { display: flex; align-items: center; justify-content: center; min-height: 100vh; }

.screen {
  position: relative; height: 550px; width: 360px;
  box-shadow: 0px 0px 24px #ceae1fff; border-radius: 20px;
  background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.2);
}

.screen__content { z-index: 1; position: relative; height: 100%; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }

.login-header { text-align: center; margin-bottom: 30px; }
.login-header img { width: 100px; height: auto; display: block; margin: 0 auto 10px; }
.login-header h1 { color: #e7d25bff; font-family: 'Cursive','Brush Script MT',sans-serif; font-size: 3rem; font-weight: 100; }

.login { width: 100%; }
.login__field { padding: 20px 0px; position: relative; }
.login__icon { position: absolute; top: 30px; color: #e7d25bff; }
.login__input { border: none; border-bottom: 2px solid #D1D1D4; background: none; padding: 10px 10px 10px 24px; font-weight: 700; width: 100%; transition: .2s; color: #fff; }
.login__input:focus, .login__input:hover { outline: none; border-bottom-color: #e7d25bff; }

.eye-icon { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); cursor: pointer; color: #e7d25bff; font-size: 18px; }
.eye-icon:hover { color: #fff; cursor: pointer;  transition: color 0.2s ease; }
.login__submit {
  padding: 15px 25px; border: 0; border-radius: 15px; color: #6d611bff; z-index: 1;
  background: #e8e8e8; font-weight: 1000; font-size: 17px;
  box-shadow: 4px 8px 19px -3px rgba(0,0,0,0.27); transition: all 250ms;
  margin-left: 105px; margin-top: 20px; overflow: hidden;
  display: flex; align-items: center; justify-content: center;
}
.login__submit:hover { color: #e8e8e8; background-color: #e7d25bff; transition: 0.2s;}
.login__submit:active { transform: scale(0.8); }

.extra-links { margin-top: 1px; text-align: center; }
.extra-links a { display: block; font-size: 0.9rem; color: #FFD93D; text-decoration: underline; margin: 5px 0; cursor: pointer; }
.extra-links a:hover { color: #fff; }

.error { color: #ff5c5c; text-align: center; margin-top: 0px; font-size: 0.9rem; }

.back-btn {
  position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 1rem; font-weight: bold;
  color: #fff; background: #74512d; border-radius: 20px; text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3); transition: background 0.3s ease, transform 0.2s ease; z-index: 1000;
}
.back-btn:hover { background: #feba17; color: #333; transform: scale(1.05); }
</style>
</head>
<body>
<a href="admin-dashboard.php" class="back-btn">⬅ Back</a>

<div class="container">
  <div class="screen">
    <div class="screen__content">
      <div class="login-header">
        <img src="images/bee.png" alt="Bee Logo">
        <h1>HiveCare Database Login</h1>
      </div>

      <form class="login" method="POST">
        <div class="login__field">
          <i class="login__icon fas fa-user-shield"></i>
          <input type="text" name="username" class="login__input" placeholder="DB Username" required>
        </div>
        <div class="login__field">
          <i class="login__icon fas fa-lock"></i>
          <input type="password" name="password" id="password" class="login__input" placeholder="Password" required>
          <span class="eye-icon" onclick="togglePassword('password')">&#128065;</span>
        </div>
        <button type="submit" class="button login__submit">
          <span class="button__text">Log In</span>
          <i class="button__icon fas fa-chevron-right"></i>
        </button>
      </form>

      <div class="extra-links">
        <a href="database-forgotpassword.php">Forgot Password?</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function togglePassword(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
