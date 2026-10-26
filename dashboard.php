<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('b1.jpg') no-repeat center center/cover;
  height: 100vh;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(5px);
  z-index: 0;
}
.dashboard {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.15);
  padding: 50px 60px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}
h2 {
  font-size: 26px;
  font-weight: 600;
  margin-bottom: 25px;
}
a.btn {
  display: block;
  text-decoration: none;
  background: linear-gradient(135deg, #0072ff, #00c6ff);
  color: white;
  padding: 12px 20px;
  border-radius: 10px;
  margin: 10px 0;
  transition: 0.3s;
  font-weight: 500;
}
a.btn:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  transform: translateY(-2px);
  box-shadow: 0 0 10px rgba(0,114,255,0.8);
}
.logout {
  background: #ff4b5c;
}
.logout:hover {
  background: #ff6b7c;
}
</style>
</head>
<body>
  <div class="dashboard">
    <h2>Welcome, <?= htmlspecialchars($username) ?> 👋</h2>
    <a href="booking.php" class="btn">🚗 Book a Parking Slot</a>
    <a href="view_bookings.php" class="btn">📋 View My Bookings</a>
    <a href="logout.php" class="btn logout">🔒 Logout</a>
  </div>
</body>
</html>
