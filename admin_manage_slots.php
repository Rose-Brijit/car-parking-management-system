<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php';
$slots = $conn->query("SELECT * FROM parking_slots")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Parking Slots</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Parking Slot Management</h2>
    <table border="1">
        <tr><th>ID</th><th>Slot No</th><th>Status</th></tr>
        <?php foreach ($slots as $slot): ?>
        <tr>
            <td><?= $slot['id'] ?></td>
            <td><?= $slot['slot_no'] ?></td>
            <td><?= $slot['status'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
