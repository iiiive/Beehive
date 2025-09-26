<?php
session_start();
include("../config.php");

// For testing purposes: if session not set, assume admin_id = 1
$admin_id = $_SESSION['admin_id'] ?? 1;  

$success = $error = "";

// Fetch current admin info
$sql = "SELECT username, email FROM admins WHERE admin_id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    die("Admin not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admins SET username = ?, email = ?, password_hash = ? WHERE admin_id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $password_hash, $admin_id);
    } else {
        $sql = "UPDATE admins SET username = ?, email = ? WHERE admin_id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $admin_id);
    }

    if ($stmt->execute()) {
        $success = " Profile updated successfully!";
        $_SESSION['username'] = $username; 
    } else {
        $error = " Error: " . $stmt->error;
    }
}

if (isset($stmt)) $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Profile - HiveCare</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Raleway, sans-serif;
}
body {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('images/profile_addusers.jpeg') no-repeat center center/cover;
  filter: brightness(25%);
  z-index: -1;
}
.container {
  width: 480px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 0px 0px 24px #ceae1fff;
  padding: 30px;
  animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  text-align: center;
  color: #e7d25bff;
  margin-bottom: 25px;
  font-size: 26px;
}
form {
  display: flex;
  flex-direction: column;
}
.form-group {
  margin-bottom: 18px;
}
form input {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  font-weight: bold;
  transition: all 0.3s ease;
}
form input::placeholder {
  color: #ddd;
}
form input:focus {
  outline: none;
  border: 2px solid #e7d25bff;
  background: rgba(255, 255, 255, 0.25);
}
button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  background: #e7d25bff;
  color: #333;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}
button:hover {
  background: #cdbd49;
  color: #000;
  transform: translateY(-2px);
}
button:active {
  transform: scale(0.95);
}
.success, .error {
  text-align: center;
  margin-top: 15px;
  font-weight: bold;
}
.success { 
    color: #299b29ff; 
    margin-bottom: 30px; 
}
.error { 
    color: #ec2f2fff; 
    
}

/* Back Button */
.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: bold;
  color: #333;
  background: #e7d25bff;
  border-radius: 20px;
  text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
  transition: background 0.3s ease, transform 0.2s ease;
  z-index: 1000;
}
.back-btn:hover {
  background: #e7d25bff;
  color: #333;
  transform: scale(1.05);
}
</style>
</head>
<body>

<a href="admin-dashboard.php" class="back-btn">⬅ Back</a>

<div class="container">
  <h2>Admin Profile</h2>

  <?php if (!empty($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php elseif (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($admin['username']) ?>" required>
    </div>
    <div class="form-group">
      <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($admin['email']) ?>" required>
    </div>
    <div class="form-group">
      <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
    </div>
    <button type="submit">Update Profile</button>
  </form>
</div>
</body>
</html>
