<?php
// parking_slots_view.php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../users_login.php"); exit; }
if (file_exists(__DIR__ . '/db.php')) {
    include_once __DIR__ . '/db.php';
} else {
    include_once __DIR__ . '/../db.php';
}

$stmt = $conn->prepare("SELECT slot_id, slot_type, is_occupied, price_per_hour FROM parking_slots ORDER BY slot_id");
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Parking Slots</title></head>
<body>
  <h2>Parking Slots</h2>
  <table border="1" cellpadding="6">
    <tr><th>Slot ID</th><th>Type</th><th>Price/hr (₹)</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['slot_id']) ?></td>
        <td><?= htmlspecialchars($row['slot_type']) ?></td>
        <td><?= htmlspecialchars($row['price_per_hour']) ?></td>
        <td><?= $row['is_occupied'] ? '<span style="color:red">Occupied</span>' : '<span style="color:green">Free</span>' ?></td>
        <td>
          <?php if (!$row['is_occupied']): ?>
            <form style="display:inline" method="post" action="../booking/booking_create.php">
              <input type="hidden" name="slot_id" value="<?= $row['slot_id'] ?>">
              <button type="submit">Book</button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <br><a href="../dashboard.php">Back to Dashboard</a>
</body>
</html>
