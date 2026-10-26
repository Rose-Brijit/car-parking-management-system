<?php
session_start();
include("db.php");

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$message = "";

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entry_time = $_POST['entry'];
    $exit_time = $_POST['exit'];

    if (!empty($entry_time) && !empty($exit_time)) {
        // Find an available slot
        $slot_result = $conn->query("SELECT * FROM parking_slots WHERE availability='available' LIMIT 1");

        if ($slot_result && $slot_result->num_rows > 0) {
            $slot = $slot_result->fetch_assoc();
            $slot_no = $slot['slot_no'];

            // Calculate charge
            $entry = new DateTime($entry_time);
            $exit = new DateTime($exit_time);
            $hours = $exit->diff($entry)->h + ($exit->diff($entry)->days * 24);
            if ($hours == 0) $hours = 1;
            $charge = $hours * 20;

            // Insert booking
            $stmt = $conn->prepare("INSERT INTO booking (user_id, slot_no, entry_time, exit_time, charge) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssd", $user_id, $slot_no, $entry_time, $exit_time, $charge);
            $stmt->execute();

            // Update slot status
            $conn->query("UPDATE parking_slots SET availability='booked' WHERE slot_no='$slot_no'");

            $message = "✅ Slot $slot_no booked successfully! Charge: ₹$charge";
        } else {
            $message = "❌ No parking slots available.";
        }
    } else {
        $message = "⚠️ Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Parking Slot</title>

<style>
body {
  background: url('b1.jpg') no-repeat center center/cover;
  font-family: 'Poppins', sans-serif;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  position: relative;
  color: #fff;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(2px);
  z-index: 0;
  pointer-events: none;
}
.glass-card {
  position: relative;
  z-index: 1;
  width: 90%;
  max-width: 420px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 20px;
  padding: 40px 35px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.5);
  text-align: center;
  backdrop-filter: blur(10px);
}
h2 {
  margin-bottom: 20px;
  color: #ffffff;
  font-weight: 600;
  letter-spacing: 0.5px;
}
input[type="datetime-local"] {
  width: 100%;
  padding: 12px 15px;
  border-radius: 10px;
  border: none;
  margin-bottom: 20px;
  background: rgba(255,255,255,0.25);
  color: #fff;
  font-size: 15px;
  outline: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
input::placeholder {
  color: #e3e3e3;
}
button {
  width: 100%;
  padding: 12px 20px;
  border-radius: 10px;
  background: linear-gradient(135deg, #0072ff, #00c6ff);
  color: white;
  font-size: 16px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,114,255,0.5);
  transition: all 0.3s ease;
}
button:hover {
  background: linear-gradient(135deg, #00c6ff, #0072ff);
  transform: translateY(-2px);
}
a.back-btn {
  display: inline-block;
  margin-top: 18px;
  padding: 10px 25px;
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
  color: #fff;
  text-decoration: none;
  transition: all 0.3s ease;
}
a.back-btn:hover {
  background: rgba(255,255,255,0.35);
}
.message {
  font-size: 15px;
  font-weight: 500;
  color: #00ffff;
  margin-bottom: 15px;
}
</style>
</head>

<body>
  <div class="glass-card">
    <h2>Book Parking Slot</h2>

    <?php if ($message != ""): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="datetime-local" name="entry" placeholder="Entry Date & Time" required><br>
        <input type="datetime-local" name="exit" placeholder="Exit Date & Time" required><br>
        <button type="submit">Confirm Booking</button>
    </form>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
  </div>
</body>
</html>
