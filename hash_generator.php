<?php
$password = 'admin123'; // Your desired password for the admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password for 'admin123': " . $hashed_password;
?>