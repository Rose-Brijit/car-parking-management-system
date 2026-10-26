<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php';
$bookings = $conn->query("SELECT b.id, u.username, b.slot_no, b.status, b.time 
                          FROM bookings b 
                          JOIN users u ON b.user_id = u.id")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>All Bookings</h2>
    <table border="1">
        <tr><th>ID</th><th>User</th><th>Slot No</th><th>Status</th><th>Time</th></tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['username'] ?></td>
            <td><?= $b['slot_no'] ?></td>
            <td><?= $b['status'] ?></td>
            <td><?= $b['time'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
