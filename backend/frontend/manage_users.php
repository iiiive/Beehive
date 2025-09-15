<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

require_once "../config.php"; // your DB connection

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
        $admin_id = 1; // later replace with $_SESSION['admin_id'] if you store it
        $stmt->bind_param("ssssssssi", $firstname, $lastname, $username, $email, $password, $birthday, $address, $contact_number, $admin_id);
        
        if ($stmt->execute()) {
            $success = "User account created successfully!";
        } else {
            $error = "Error creating user: " . $stmt->error;
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
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
body {
  font-family: Raleway, sans-serif;
  background: #f8f8f8;
  padding: 40px;
}
.container {
  width: 500px;
  margin: 0 auto;
  background: #fff;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #6d611bff;
  margin-bottom: 20px;
}
form label {
  font-weight: bold;
  margin-top: 10px;
  display: block;
}
form input, form textarea {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 8px;
  border: 1px solid #ccc;
}
button {
  width: 100%;
  padding: 12px;
  margin-top: 20px;
  border: none;
  background: #e7d25bff;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
}
button:hover {
  background: #cdbd49;
}
.success { color: green; text-align: center; margin-top: 10px; }
.error { color: red; text-align: center; margin-top: 10px; }
</style>
</head>
<body>
<div class="container">
  <h2>Create Worker Account</h2>
  <form method="POST">
    <label>First Name</label>
    <input type="text" name="firstname" required>
    
    <label>Last Name</label>
    <input type="text" name="lastname" required>
    
    <label>Username</label>
    <input type="text" name="username" required>
    
    <label>Email</label>
    <input type="email" name="email">
    
    <label>Password</label>
    <input type="password" name="password" required>
    
    <label>Birthday</label>
    <input type="date" name="birthday">
    
    <label>Address</label>
    <textarea name="address"></textarea>
    
    <label>Contact Number</label>
    <input type="text" name="contact_number">
    
    <button type="submit">Create Account</button>
  </form>

  <?php if (!empty($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php elseif (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
</div>
</body>
</html>
