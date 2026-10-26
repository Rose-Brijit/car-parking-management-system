<?php
// payment_process.php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../users_login.php"); exit; }
if (file_exists(__DIR__ . '/db.php')) {
    include_once __DIR__ . '/db.php';
} else {
    include_once __DIR__ . '/../db.php';
}

$booking_id = intval($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);
if ($booking_id <= 0) { echo "Invalid booking. <a href='../dashboard.php'>Back</a>"; exit; }

// Fetch booking + slot rate
$stmt = $conn->prepare("SELECT b.booking_id, b.user_id, b.slot_id, b.entry_time, b.payment_status, p.price_per_hour
                        FROM booking b JOIN parking_slots p ON b.slot_id = p.slot_id
                        WHERE b.booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) { echo "Booking not found. <a href='../dashboard.php'>Back</a>"; exit; }
$booking = $res->fetch_assoc();
$stmt->close();

// If POST -> process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['method'])) {
    $method = $conn->real_escape_string($_POST['method']);

    // Calculate duration and total cost
    $entry = new DateTime($booking['entry_time']);
    $exit = new DateTime(); // now
    $diff = $exit->diff($entry);
    $hours = max(1, ($diff->days * 24) + $diff->h + (int)($diff->i > 0 ? 1 : 0)); // round up if minutes > 0
    $rate = floatval($booking['price_per_hour']);
    $total = $hours * $rate;

    // Transaction
    $conn->begin_transaction();
    $pstmt = $conn->prepare("INSERT INTO payment (booking_id, amount, method, status, payment_time) VALUES (?, ?, ?, 'Success', NOW())");
    $pstmt->bind_param("ids", $booking_id, $total, $method);
    $okp = $pstmt->execute();
    $pstmt->close();

    // Update booking
    $upb = $conn->prepare("UPDATE booking SET exit_time = NOW(), total_cost = ?, payment_status = 'Paid' WHERE booking_id = ?");
    $upb->bind_param("di", $total, $booking_id);
    $okb = $upb->execute();
    $upb->close();

    // Free slot
    $ups = $conn->prepare("UPDATE parking_slots SET is_occupied = 0 WHERE slot_id = ?");
    $ups->bind_param("i", $booking['slot_id']);
    $oks = $ups->execute();
    $ups->close();

    // Log
    $log = $conn->prepare("INSERT INTO activity_log (user_id, action, log_time) VALUES (?, ?, NOW())");
    $action = "Payment success for booking_id: $booking_id amount: ₹$total method: $method";
    $log->bind_param("is", $booking['user_id'], $action);
    $okl = $log->execute();
    $log->close();

    if ($okp && $okb && $oks && $okl) {
        $conn->commit();
        header("Location: payment_success.php?booking_id=".$booking_id);
        exit;
    } else {
        $conn->rollback();
        echo "Payment failed. Try again. <a href='../dashboard.php'>Back</a>";
        exit;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Payment</title></head>
<body>
  <h2>Payment for Booking #<?= htmlspecialchars($booking['booking_id']) ?></h2>
  <p>Entry Time: <?= htmlspecialchars($booking['entry_time']) ?></p>
  <p>Rate: ₹<?= htmlspecialchars($booking['price_per_hour']) ?>/hr</p>
  <form method="post" action="">
    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
    <label>Payment Method</label><br>
    <select name="method" required>
      <option value="UPI">UPI</option>
      <option value="Wallet">Wallet</option>
      <option value="Card">Credit/Debit Card</option>
    </select><br><br>
    <button type="submit">Simulate Pay Now</button>
  </form>
  <br><a href="../dashboard.php">Cancel</a>
</body>
</html>
