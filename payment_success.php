<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../users_login.php"); exit; }
if (file_exists(__DIR__ . '/db.php')) {
    include_once __DIR__ . '/db.php';
} else {
    include_once __DIR__ . '/../db.php';
}
$booking_id = intval($_GET['booking_id'] ?? 0);
if ($booking_id <= 0) { echo "Invalid. <a href='../dashboard.php'>Back</a>"; exit; }

// Show payment & booking summary
$stmt = $conn->prepare("SELECT b.booking_id, b.user_id, b.slot_id, b.entry_time, b.exit_time, b.total_cost, p.method
                        FROM booking b
                        LEFT JOIN payment p ON p.booking_id = b.booking_id
                        WHERE b.booking_id = ? ORDER BY p.payment_time DESC LIMIT 1");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo "<h2>Payment Successful</h2>";
    echo "<p>Booking ID: " . htmlspecialchars($row['booking_id']) . "</p>";
    echo "<p>Slot ID: " . htmlspecialchars($row['slot_id']) . "</p>";
    echo "<p>Entry: " . htmlspecialchars($row['entry_time']) . "</p>";
    echo "<p>Exit: " . htmlspecialchars($row['exit_time']) . "</p>";
    echo "<p>Amount Paid: ₹" . htmlspecialchars($row['total_cost']) . "</p>";
} else {
    echo "<p>Details not found.</p>";
}
echo '<br><a href="../dashboard.php">Back to Dashboard</a>';
$stmt->close();
