<?php
// run this small script once (save as create_admin.php and run via browser or CLI)
include 'db.php';

$username = 'admin';
$plain_password = 'admin123';
$hash = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hash);
if ($stmt->execute()) {
    echo "Admin created with hashed password.";
} else {
    echo "Error: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
