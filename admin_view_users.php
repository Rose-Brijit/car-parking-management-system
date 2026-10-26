<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php';
$users = $conn->query("SELECT * FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Registered Users</h2>
    <table border="1">
        <tr><th>ID</th><th>Username</th><th>Email</th></tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['email'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
