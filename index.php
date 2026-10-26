<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System</title>
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
  background: rgba(0, 0, 0, 0.55); /* dim overlay */
  backdrop-filter: blur(4px);
  z-index: 0;
}

.container {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 15px;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.4);
  padding: 40px;
  text-align: center;
  backdrop-filter: blur(8px);
  min-width: 320px;
}

h1 {
  font-size: 26px;
  margin-bottom: 30px;
}

.btn-group {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

a.btn {
  display: block;
  text-decoration: none;
  background: linear-gradient(135deg, #0072ff, #00c6ff);
  color: white;
  padding: 12px 25px;
  border-radius: 10px;
  font-weight: 600;
  transition: 0.3s;
}

a.btn:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,114,255,0.6);
}

a.btn.admin {
  background: linear-gradient(135deg, #ff7b00, #ffae00);
}

a.btn.admin:hover {
  background: linear-gradient(135deg, #ffae00, #ff7b00);
  box-shadow: 0 4px 15px rgba(255,123,0,0.6);
}
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Welcome to Smart Parking System</h1>
        <div class="btn-group">
            <a href="login.php" class="btn">User Login</a>
            <a href="register.php" class="btn">User Register</a>
            <a href="admin_login.php" class="btn admin">Admin Portal</a>
        </div>
    </div>
</body>
</html>