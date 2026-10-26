<?php
include 'db.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        $error = "All fields are required!";
    } else {
        // Check if username already exists
        $check = $conn->query("SELECT * FROM users WHERE username='$username'");
        if ($check && $check->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            if ($conn->query($query)) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  margin: 0;
  color: white;
}
.container {
  background: rgba(255,255,255,0.1);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  width: 300px;
  text-align: center;
}
input {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: none;
  border-radius: 8px;
  outline: none;
}
button {
  width: 100%;
  padding: 10px;
  background: white;
  color: #0072ff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: #0072ff;
  color: white;
}
.message {
  font-weight: bold;
  margin-bottom: 10px;
}
.error { color: #ffb3b3; }
.success { color: #00ffcc; }
</style>
</head>
<body>
<div class="container">
  <h2>Register</h2>
  <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="message success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Enter username" required><br>
    <input type="password" name="password" placeholder="Enter password" required><br>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="login.php" style="color:white;">Login</a></p>
</div>
</body>
</html>
