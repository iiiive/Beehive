<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

require_once "../config.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birthday = $_POST['birthday'];
    $address = trim($_POST['address']);
    $contact_number = trim($_POST['contact_number']);

    $sql = "INSERT INTO users (firstname, lastname, username, email, password_hash, birthday, address, contact_number, created_by_admin_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $link->prepare($sql)) {
        $admin_id = 1; // Replace later with $_SESSION['admin_id']
        $stmt->bind_param("ssssssssi", $firstname, $lastname, $username, $email, $password, $birthday, $address, $contact_number, $admin_id);
        
        if ($stmt->execute()) {
            $success = "Worker account created successfully!";
        } else {
            $error = " Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Worker Account</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
form input, form textarea {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  font-weight: bold;
  transition: all 0.3s ease;
}
form input::placeholder, form textarea::placeholder {
  color: #ddd;
}
form input:focus, form textarea:focus {
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
.success { color: lightgreen; }
.error { color: #ff7b7b; }

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
  <h2> Create Worker Account</h2>
  <form method="POST">
    <div class="form-group"><input type="text" name="firstname" placeholder="First Name" required></div>
    <div class="form-group"><input type="text" name="lastname" placeholder="Last Name" required></div>
    <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
    <div class="form-group"><input type="email" name="email" placeholder="Email"></div>
    <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
    <div class="form-group"><input type="date" name="birthday"></div>
    <div class="form-group"><textarea name="address" placeholder="Address"></textarea></div>
    <div class="form-group"><input type="text" name="contact_number" placeholder="Contact Number"></div>
    <button type="submit"> Create Account</button>
  </form>

  <?php if (!empty($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php elseif (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
</div>
