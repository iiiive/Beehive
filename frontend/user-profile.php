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
        $success = "✅ Profile updated successfully!";
        $_SESSION['username'] = $username; // optional: update session username
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
<title>User Profile - HiveCare</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
body { font-family: Raleway, sans-serif; background: url('images/beehive.jpeg') no-repeat center center/cover; filter: brightness(20%); display: flex; justify-content: center; align-items: center; height: 100vh; }
.container { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); border-radius: 20px; padding: 40px; width: 400px; box-shadow: 0 0 24px #ceae1fff; text-align: center; }
h2 { margin-bottom: 20px; color: #e7d25b; }
input, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
button { width: 100%; padding: 12px; border-radius: 10px; border: none; background: #e7d25b; color: #6d611b; font-weight: bold; cursor: pointer; }
button:hover { background: #cdbd49; color: #000; }
.success { color: lightgreen; text-align: center; margin-bottom: 10px; }
.error { color: red; text-align: center; margin-bottom: 10px; }
</style>
</head>
<body>
<div class="container">
<h2>User Profile</h2>

<?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <input type="text" name="firstname" placeholder="First Name" value="<?= htmlspecialchars($user['firstname']) ?>" required>
    <input type="text" name="lastname" placeholder="Last Name" value="<?= htmlspecialchars($user['lastname']) ?>" required>
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username']) ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <input type="date" name="birthday" placeholder="Birthday" value="<?= htmlspecialchars($user['birthday']) ?>">
    <textarea name="address" placeholder="Address"><?= htmlspecialchars($user['address']) ?></textarea>
    <input type="text" name="contact_number" placeholder="Contact Number" value="<?= htmlspecialchars($user['contact_number']) ?>">
    <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
    <button type="submit">Update Profile</button>
</form>
</div>
</body>
</html>
