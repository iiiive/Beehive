<?php
session_start();
include("../config.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Fetch current user info
$sql = "SELECT firstname, lastname, username, email, birthday, address, contact_number FROM users WHERE user_id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $birthday = $_POST['birthday'];
    $address = trim($_POST['address']);
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password']; // optional

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET firstname=?, lastname=?, username=?, email=?, birthday=?, address=?, contact_number=?, password_hash=? WHERE user_id=?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ssssssssi", $firstname, $lastname, $username, $email, $birthday, $address, $contact_number, $password_hash, $user_id);
    } else {
        $sql = "UPDATE users SET firstname=?, lastname=?, username=?, email=?, birthday=?, address=?, contact_number=? WHERE user_id=?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("sssssssi", $firstname, $lastname, $username, $email, $birthday, $address, $contact_number, $user_id);
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
<title>User Profile - HiveCare</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: Raleway, sans-serif; }
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
  background: url('images/beehive.jpeg') no-repeat center center/cover;
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
h2 { text-align: center; color: #e7d25bff; margin-bottom: 25px; font-size: 26px; }
form { display: flex; flex-direction: column; }
.form-group { margin-bottom: 18px; }
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
form input::placeholder, form textarea::placeholder { color: #ddd; }
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
  color: #6d611bff;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}
button:hover { background: #cdbd49; color: #000; transform: translateY(-2px); }
button:active { transform: scale(0.95); }
.success, .error { text-align: center; margin-top: 15px; font-weight: bold; }
.success { 
    color: #4c8b27ff; 
    margin-bottom: 30px;
}
.error { color: #b42e2eff;
    margin-bottom: 30px;
 }
/* Back Button */
.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: bold;
  color: #fff;
  background: #74512d;
  border-radius: 20px;
  text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
  transition: background 0.3s ease, transform 0.2s ease;
  z-index: 1000;
}
.back-btn:hover { background: #feba17; color: #333; transform: scale(1.05); }
</style>
</head>
<body>

<a href="user-dashboard.php" class="back-btn">⬅ Back</a>

<div class="container">
<h2>User Profile</h2>

<?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
  <div class="form-group">
    <input type="text" name="firstname" placeholder="First Name" value="<?= htmlspecialchars($user['firstname']) ?>" required>
  </div>
  <div class="form-group">
    <input type="text" name="lastname" placeholder="Last Name" value="<?= htmlspecialchars($user['lastname']) ?>" required>
  </div>
  <div class="form-group">
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username']) ?>" required>
  </div>
  <div class="form-group">
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
  </div>
  <div class="form-group">
    <input type="date" name="birthday" placeholder="Birthday" value="<?= htmlspecialchars($user['birthday']) ?>">
  </div>
  <div class="form-group">
    <textarea name="address" placeholder="Address"><?= htmlspecialchars($user['address']) ?></textarea>
  </div>
  <div class="form-group">
    <input type="text" name="contact_number" placeholder="Contact Number" value="<?= htmlspecialchars($user['contact_number']) ?>">
  </div>
  <div class="form-group">
    <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
  </div>
  <button type="submit">Update Profile</button>
</form>
</div>
</body>
</html>
