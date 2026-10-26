<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        $error = "⚠️ Please fill in all fields!";
    } else {
        // Match with admin table
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // ✅ Use correct column names
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];

            // Redirect to dashboard
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid username or password!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #1e3c72, #2a5298);
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
  width: 320px;
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
  color: #1e3c72;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: #00c6ff;
  color: white;
}
.error {
  color: #ffb3b3;
  font-weight: bold;
  margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="container">
  <h2>Admin Login</h2>
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <input type="text" name="username" placeholder="Enter username" required><br>
    <input type="password" name="password" placeholder="Enter password" required><br>
    <button type="submit" name="login">Login</button>
  </form>
</div>
</body>
</html>
