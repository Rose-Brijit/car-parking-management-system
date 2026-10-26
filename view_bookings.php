<?php
session_start();
include("db.php");

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'User';

// Fetch parking slots
$query = "SELECT * FROM parking_slots ORDER BY slot_no ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Parking Slots</title>

<style>
body {
  background: url('b1.jpg') no-repeat center center/cover;
  font-family: 'Poppins', sans-serif;
  height: 100vh;
  margin: 0;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(3px);
  z-index: 0;
}
.container {
  position: relative;
  z-index: 1;
  width: 90%;
  max-width: 900px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.5);
  padding: 40px;
  text-align: center;
  backdrop-filter: blur(10px);
}
h2 {
  color: #fff;
  margin-bottom: 25px;
  font-weight: 600;
  letter-spacing: 1px;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
  gap: 20px;
  justify-items: center;
}
.slot {
  width: 90px;
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 15px;
  font-weight: 600;
  font-size: 16px;
  color: white;
  cursor: default;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.slot.available {
  background: rgba(0, 255, 100, 0.75);
  box-shadow: 0 0 10px rgba(0,255,100,0.6);
}
.slot.booked {
  background: rgba(255, 60, 60, 0.75);
  box-shadow: 0 0 10px rgba(255,60,60,0.6);
}
.slot:hover {
  transform: scale(1.1);
  box-shadow: 0 0 20px rgba(255,255,255,0.8);
}
.back-btn {
  display: inline-block;
  margin-top: 30px;
  padding: 10px 25px;
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
  color: #fff;
  text-decoration: none;
  transition: all 0.3s ease;
}
.back-btn:hover {
  background: rgba(255,255,255,0.35);
}
.legend {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 25px;
  font-size: 14px;
}
.legend span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.legend-box {
  width: 20px;
  height: 20px;
  border-radius: 5px;
  display: inline-block;
}
.available-box {
  background: rgba(0, 255, 100, 0.75);
}
.booked-box {
  background: rgba(255, 60, 60, 0.75);
}
</style>
</head>
<body>
<div class="container">
  <h2>🚗 Parking Slot Availability</h2>

  <div class="grid">
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $slot_class = ($row['availability'] == 'available') ? 'available' : 'booked';
            echo "<div class='slot $slot_class'>{$row['slot_no']}</div>";
        }
    } else {
        echo "<p>No slots found in the database.</p>";
    }
    ?>
  </div>

  <div class="legend">
    <span><span class="legend-box available-box"></span> Available</span>
    <span><span class="legend-box booked-box"></span> Booked</span>
  </div>

  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
