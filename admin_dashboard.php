<?php
session_start();
include 'db.php';

// ✅ Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_name = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #1e3c72, #2a5298);
  color: white;
  margin: 0;
  padding: 0;
}
.header {
  background: rgba(255,255,255,0.15);
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.header h2 {
  margin: 0;
}
.header a {
  background: white;
  color: #1e3c72;
  text-decoration: none;
  padding: 8px 18px;
  border-radius: 8px;
  transition: 0.3s;
}
.header a:hover {
  background: #00c6ff;
  color: white;
}
.container {
  padding: 30px 40px;
}
.card {
  background: rgba(255,255,255,0.1);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 30px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.card h3 {
  margin-top: 0;
  color: #00c6ff;
  border-bottom: 1px solid rgba(255,255,255,0.3);
  padding-bottom: 10px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}
th {
  color: #00c6ff;
}
tr:hover {
  background: rgba(255,255,255,0.1);
}
.no-data {
  color: #ffb3b3;
  text-align: center;
  padding: 10px;
}
.status-available {
  color: #90ee90;
  font-weight: bold;
}
.status-booked {
  color: #ff6961;
  font-weight: bold;
}
</style>
</head>
<body>

<div class="header">
  <h2>👨‍💼 Admin Dashboard</h2>
  <div>
    <span>Welcome, <?= htmlspecialchars($admin_name) ?> 👋</span>
    &nbsp;&nbsp;
    <a href="admin_logout.php">Logout</a>
  </div>
</div>

<div class="container">

  <!-- USERS TABLE -->
  <div class="card">
    <h3>👥 Registered Users</h3>
    <table>
      <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Role</th>
      </tr>
      <?php
      $users = $conn->query("SELECT user_id, username, role FROM users");
      if ($users && $users->num_rows > 0) {
          while ($row = $users->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['user_id']}</td>
                      <td>{$row['username']}</td>
                      <td>{$row['role']}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='3' class='no-data'>No users found.</td></tr>";
      }
      ?>
    </table>
  </div>

  <!-- PARKING SLOTS TABLE -->
  <div class="card">
    <h3>🚗 Parking Slots</h3>
    <table>
      <tr>
        <th>Slot ID</th>
        <th>Slot No</th>
        <th>Type</th>
        <th>Availability</th>
        <th>Status</th>
      </tr>
      <?php
      $slots = $conn->query("SELECT * FROM parking_slots");
      if ($slots && $slots->num_rows > 0) {
          while ($row = $slots->fetch_assoc()) {
              $availClass = $row['availability'] === 'available' ? 'status-available' : 'status-booked';
              echo "<tr>
                      <td>{$row['slot_id']}</td>
                      <td>{$row['slot_no']}</td>
                      <td>{$row['slot_type']}</td>
                      <td class='{$availClass}'>{$row['availability']}</td>
                      <td>{$row['status']}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5' class='no-data'>No parking slots found.</td></tr>";
      }
      ?>
    </table>
  </div>

  <!-- BOOKINGS TABLE -->
  <div class="card">
    <h3>🧾 Bookings</h3>
    <table>
      <tr>
        <th>Booking ID</th>
        <th>User ID</th>
        <th>Slot No</th>
        <th>Entry Time</th>
        <th>Exit Time</th>
        <th>Charge (₹)</th>
      </tr>
      <?php
      $bookings = $conn->query("SELECT * FROM booking ORDER BY booking_id DESC");
      if ($bookings && $bookings->num_rows > 0) {
          while ($row = $bookings->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['booking_id']}</td>
                      <td>{$row['user_id']}</td>
                      <td>{$row['slot_no']}</td>
                      <td>{$row['entry_time']}</td>
                      <td>{$row['exit_time']}</td>
                      <td>{$row['charge']}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='6' class='no-data'>No bookings found.</td></tr>";
      }
      ?>
    </table>
  </div>

</div>

</body>
</html>
