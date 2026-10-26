<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../users_login.php"); exit; }
if (file_exists(__DIR__ . '/db.php')) {
    include_once __DIR__ . '/db.php';
} else {
    include_once __DIR__ . '/../db.php';
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT al.log_id, al.user_id, u.username, al.action, al.log_time FROM activity_log al LEFT JOIN users u ON al.user_id=u.user_id ORDER BY al.log_time DESC LIMIT 200");
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT log_id, user_id, action, log_time FROM activity_log WHERE user_id = ? ORDER BY log_time DESC LIMIT 200");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Activity Log</title></head>
<body>
  <h2>Activity Log</h2>
  <table border="1" cellpadding="6">
    <tr><th>Time</th><th>User</th><th>Action</th></tr>
    <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['log_time']) ?></td>
        <td><?= htmlspecialchars($r['username'] ?? $r['user_id']) ?></td>
        <td><?= htmlspecialchars($r['action']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <br><a href="../dashboard.php">Back to Dashboard</a>
</body>
</html>
