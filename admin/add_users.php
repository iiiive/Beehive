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
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');

    // --- Validation ---
    if (!preg_match("/^[a-zA-Z ]+$/", $firstname)) {
        $error = "First name can only contain letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $lastname)) {
        $error = "Last name can only contain letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        $error = "Username can contain letters and numbers only, no spaces or special characters.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // --- Check if email already exists ---
        $check = $link->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists. Please use a different email.";
        } else {
            // --- Insert new user ---
            $sql = "INSERT INTO users 
                (firstname, lastname, username, email, password_hash, birthday, address, contact_number, created_by_admin_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $link->prepare($sql)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $admin_id = $_SESSION['admin_id'] ?? 1;
                $stmt->bind_param("ssssssssi", $firstname, $lastname, $username, $email, $password_hash, $birthday, $address, $contact_number, $admin_id);

                if ($stmt->execute()) {
                    $success = "Worker account created successfully!";
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to prepare the statement: " . $link->error;
            }
        }
        $check->close();
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
* { box-sizing: border-box; margin:0; padding:0; font-family: Raleway, sans-serif; }
body { height: 100vh; display:flex; align-items:center; justify-content:center; position:relative; }
body::before { content:""; position:absolute; top:0; left:0; right:0; bottom:0; background:url('images/profile_addusers.jpeg') no-repeat center/cover; filter: brightness(25%); z-index:-1; }
.container { width:480px; background: rgba(255,255,255,0.1); border-radius:20px; backdrop-filter: blur(15px); border:1px solid rgba(255,255,255,0.2); box-shadow:0 0 24px #ceae1fff; padding:30px; }
h2 { text-align:center; color:#e7d25bff; margin-bottom:25px; font-size:26px; }
form { display:flex; flex-direction:column; }
.form-group { margin-bottom:18px; position:relative; }
form input, form textarea { width:100%; padding:12px; border-radius:10px; border:none; background: rgba(255,255,255,0.2); color:#fff; font-weight:bold; transition:0.3s; }
form input::placeholder, form textarea::placeholder { color:#ddd; }
form input:focus, form textarea:focus { outline:none; border:2px solid #e7d25bff; background: rgba(255,255,255,0.25); }
button { width:100%; padding:14px; border:none; border-radius:12px; background:#e7d25bff; color:#333; font-weight:bold; font-size:16px; cursor:pointer; transition:all 0.3s ease; }
button:hover { background:#cdbd49; color:#000; transform:translateY(-2px); }
.success, .error { text-align:center; margin-top:15px; font-weight:bold; }
.success { color:lightgreen; }
.error { color:#ff7b7b; }
.eye-icon { position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#e7d25bff; }
.eye-icon:hover { color:#fff; }
.back-btn { position:absolute; top:20px; left:20px; padding:10px 20px; font-size:1rem; font-weight:bold; color:#333; background:#e7d25bff; border-radius:20px; text-decoration:none; box-shadow:4px4px10px rgba(0,0,0,0.3); z-index:1000; transition:background 0.3s ease, transform 0.2s ease; }
.back-btn:hover { transform:scale(1.05); }
</style>
</head>
<body>

<a href="manage-users.php" class="back-btn">⬅ Back</a>

<div class="container">
<h2>Create Worker Account</h2>
<form method="POST">
    <div class="form-group"><input type="text" name="firstname" placeholder="First Name" required></div>
    <div class="form-group"><input type="text" name="lastname" placeholder="Last Name" required></div>
    <div class="form-group"><input type="text" name="username" placeholder="Username" required></div>
    <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
    
    <div class="form-group">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <span class="eye-icon" onclick="togglePassword('password')">&#128065;</span>
    </div>
    <div class="form-group">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
        <span class="eye-icon" onclick="togglePassword('confirm_password')">&#128065;</span>
    </div>

    <div class="form-group"><input type="date" name="birthday"></div>
    <div class="form-group"><textarea name="address" placeholder="Address"></textarea></div>
    <div class="form-group"><input type="text" name="contact_number" placeholder="Contact Number"></div>

    <button type="submit">Create Account</button>
</form>

<?php if (!empty($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php elseif (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
</div>

<script>
function togglePassword(id){
    const input = document.getElementById(id);
    input.type = (input.type === 'password') ? 'text' : 'password';
}
</script>
</body>
</html>
