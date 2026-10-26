<?php
require_once 'db.php';

// fetch all users
$sql = "SELECT user_id, username, email, password FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Users</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #001F3F, #0072ff);
  color: #fff;
  text-align: center;
  padding: 40px;
}
table {
  width: 80%;
  margin: auto;
  border-collapse: collapse;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
}
th, td {
  padding: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}
th { background: rgba(0,0,0,0.3); }
tr:hover { background: rgba(255,255,255,0.1); }
</style>
</head>
<body>
<h2>👥 Registered Users</h2>
<table>
<tr><th>ID</th><th>Username</th><th>Email</th><th>Password</th></tr>
<?php
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['user_id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['email']}</td>
            <td>{$row['password']}</td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='4'>No users found</td></tr>";
}
?>
</table>
</body>
</html>
