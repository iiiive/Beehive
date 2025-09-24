<?php
session_start();
include("../config.php");

// For testing purposes: if session not set, assume admin_id = 1
// In production, make sure $_SESSION['admin_id'] is set after login
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
        $success = "✅ Profile updated successfully!";
        $_SESSION['username'] = $username; // update session username if needed
    } else {
        $error = "❌ Error: " . $stmt->error;
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
body { font-family: Raleway, sans-serif; background: url('images/beehive.jpeg') no-repeat center center/cover; filter: brightness(20%); display: flex; justify-content: center; align-items: center; height: 100vh; }
.container { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); border-radius: 20px; padding: 40px; width: 400px; box-shadow: 0 0 24px #ceae1fff; text-align: center; }
h2 { margin-bottom: 20px; color: #e7d25b; }
input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
button { width: 100%; padding: 12px; border-radius: 10px; border: none; background: #e7d25b; color: #6d611b; font-weight: bold; cursor: pointer; }
button:hover { background: #cdbd49; color: #000; }
.success { color: lightgreen; text-align: center; margin-bottom: 10px; }
.error { color: red; text-align: center; margin-bottom: 10px; }
</style>
</head>
<body>
<div class="container">
<h2>Admin Profile</h2>

<?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($admin['username']) ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($admin['email']) ?>" required>
    <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
    <button type="submit">Update Profile</button>
</form>
</div>
</body>
</html>
