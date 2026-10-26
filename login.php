<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "⚠️ Please fill in all fields!";
    } else {
        // Query to match username and password
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $_SESSION['user_id'] = $user['user_id'];  // ✅ fixed: correct field name
            $_SESSION['username'] = $user['username'];

            header("Location: dashboard.php");
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
<title>User Login</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('b1.jpg') no-repeat center center/cover;
  height: 100vh;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(4px);
  z-index: 0;
}
.container {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 15px;
  padding: 40px;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  text-align: center;
  width: 320px;
}
input, button {
  width: 100%;
  padding: 12px;
  margin: 8px 0;
  border: none;
  border-radius: 8px;
  outline: none;
}
button {
  background: linear-gradient(135deg, #0072ff, #00c6ff);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
}
</style>
</head>
<body>
<div class="container">
  <h2>User Login</h2>
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <input type="text" name="username" placeholder="Enter username" required><br>
    <input type="password" name="password" placeholder="Enter password" required><br>
    <button type="submit" name="login">Login</button>
  </form>
  <p>Don’t have an account? <a href="register.php" style="color:white;">Register</a></p>
</div>
</body>
</html>
