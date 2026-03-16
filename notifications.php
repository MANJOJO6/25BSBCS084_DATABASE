<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include "config.php";

$user_id = (int)$_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Mark all as read if requested
if (isset($_GET['mark_read'])) {
    $update_sql = "UPDATE notifications SET is_read = 1 
                   WHERE user_id = $user_id AND user_type = '$user_type'";
    mysqli_query($conn, $update_sql);
    header("Location: notifications.php");
    exit;
}

// Mark single notification as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $notif_id = (int)$_GET['read'];
    $update_sql = "UPDATE notifications SET is_read = 1 
                   WHERE id = $notif_id AND user_id = $user_id AND user_type = '$user_type'";
    mysqli_query($conn, $update_sql);
    header("Location: notifications.php");
    exit;
}

// Fetch notifications
$notif_sql = "SELECT * FROM notifications 
              WHERE user_id = $user_id AND user_type = '$user_type' 
              ORDER BY created_at DESC 
              LIMIT 50";
$notif_result = mysqli_query($conn, $notif_sql);

$unread_count_sql = "SELECT COUNT(*) as count FROM notifications 
                     WHERE user_id = $user_id AND user_type = '$user_type' AND is_read = 0";
$unread_result = mysqli_query($conn, $unread_count_sql);
$unread_count = mysqli_fetch_assoc($unread_result)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%232563eb'/%3E%3Cpath d='M20 50 L40 30 L60 30 L80 50 L70 60 L50 40 L30 60 L20 50Z' fill='white'/%3E%3Ccircle cx='35' cy='45' r='5' fill='%23f59e0b'/%3E%3Ccircle cx='65' cy='45' r='5' fill='%23f59e0b'/%3E%3C/svg%3E">
<style>
.notification-container {
  max-width: 800px;
  margin: 0 auto;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.notification-badge {
  background: var(--primary);
  color: var(--white);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.notification-item {
  background: var(--white);
  border-radius: var(--radius);
  padding: 20px;
  margin-bottom: 12px;
  box-shadow: var(--shadow);
  border-left: 4px solid transparent;
  transition: all 0.2s;
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.notification-item:hover {
  transform: translateX(5px);
  box-shadow: var(--shadow-md);
}

.notification-item.unread {
  background: var(--primary-soft);
  border-left-color: var(--primary);
}

.notification-icon {
  font-size: 1.5rem;
  width: 40px;
  height: 40px;
  background: var(--gray-100);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: 700;
  color: var(--gray-800);
  margin-bottom: 4px;
  font-size: 1rem;
}

.notification-message {
  color: var(--gray-600);
  font-size: 0.9rem;
  margin-bottom: 8px;
  line-height: 1.5;
}

.notification-time {
  font-size: 0.75rem;
  color: var(--gray-500);
  display: flex;
  align-items: center;
  gap: 12px;
}

.notification-actions {
  display: flex;
  gap: 8px;
}

.notification-action {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.notification-action.mark-read {
  background: var(--primary-soft);
  color: var(--primary);
}

.notification-action.mark-read:hover {
  background: var(--primary);
  color: var(--white);
}

.empty-notifications {
  text-align: center;
  padding: 60px 20px;
  background: var(--white);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
  opacity: 0.5;
}
</style>
</head>
<body>

<nav>
  <span class="nav-brand">🏥 Homeland</span>
  <a href="dashboard.php">🏠 Dashboard</a>
  <?php if ($user_type === 'patient'): ?>
    <a href="doctors.php">👨‍⚕️ Doctors</a>
    <a href="book_appointment.php">📅 Book</a>
    <a href="my_appointments.php">🗒 My Appointments</a>
  <?php else: ?>
    <a href="doctor_appointments.php">🗒 My Schedule</a>
  <?php endif; ?>
  <a href="notifications.php" class="nav-notification" style="color: <?php echo $unread_count > 0 ? 'var(--warning)' : ''; ?>">
    🔔 Notifications <?php if ($unread_count > 0): ?><span class="notification-badge"><?php echo $unread_count; ?></span><?php endif; ?>
  </a>
  <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<div class="banner">
  <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&auto=format&fit=crop&q=70" alt="Notifications">
  <h2>🔔 Notifications</h2>
</div>

<div class="wrap">
  <div class="notification-container a1">
    <div class="notification-header">
      <h2 class="page-title">Your Notifications</h2>
      <?php if (mysqli_num_rows($notif_result) > 0): ?>
        <a href="?mark_read=1" class="btn btn-outline" style="padding: 8px 16px;">Mark all as read</a>
      <?php endif; ?>
    </div>

    <?php if (mysqli_num_rows($notif_result) === 0): ?>
      <div class="empty-notifications">
        <div class="empty-icon">🔕</div>
        <h3>No notifications</h3>
        <p style="color: var(--gray-500); margin-top: 10px;">You're all caught up!</p>
      </div>
    <?php else: ?>
      <?php while ($notif = mysqli_fetch_assoc($notif_result)): 
        $icon = '📌';
        switch($notif['type']) {
            case 'cancellation': $icon = '❌'; break;
            case 'reschedule': $icon = '📅'; break;
            case 'chat': $icon = '💬'; break;
            case 'confirmation': $icon = '✅'; break;
        }
      ?>
        <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
          <div class="notification-icon"><?php echo $icon; ?></div>
          <div class="notification-content">
            <div class="notification-title"><?php echo htmlspecialchars($notif['title']); ?></div>
            <div class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></div>
            <div class="notification-time">
              <span><?php echo date('M j, Y g:i A', strtotime($notif['created_at'])); ?></span>
              <?php if ($notif['reference_type'] === 'chat'): ?>
                <a href="chat.php?appointment_id=<?php echo $notif['reference_id']; ?>" class="btn btn-primary" style="padding: 2px 12px; font-size: 0.7rem;">Open Chat</a>
              <?php elseif ($notif['reference_type'] === 'appointment'): ?>
                <a href="<?php echo $user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'; ?>#appointment-<?php echo $notif['reference_id']; ?>" class="btn btn-primary" style="padding: 2px 12px; font-size: 0.7rem;">View</a>
              <?php endif; ?>
            </div>
          </div>
          <?php if (!$notif['is_read']): ?>
            <div class="notification-actions">
              <a href="?read=<?php echo $notif['id']; ?>" class="notification-action mark-read">✓ Mark read</a>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>