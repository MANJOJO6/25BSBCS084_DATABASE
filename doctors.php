<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?type=patient");
    exit;
}
include "config.php";

$result = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name ASC");
$total  = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Doctors – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
  <span class="nav-brand">🏥 Homeland</span>
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="doctors.php">👨‍⚕️ Doctors</a>
  <a href="book_appointment.php">📅 Book</a>
  <a href="my_appointments.php">🗒 My Appointments</a>
  <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<div class="banner">
  <img src="https://images.unsplash.com/photo-1551190822-a9333d879b1f?w=1200&auto=format&fit=crop&q=70" alt="Doctors">
  <h2>👨‍⚕️ Our Doctors</h2>
</div>

<div class="wrap">
  <h2 class="page-title">Meet Our Specialists</h2>
  <p class="page-sub"><?php echo $total; ?> experienced medical professionals ready to serve you.</p>

  <?php if ($total === 0): ?>
    <div class="empty">
      <span class="eic">👨‍⚕️</span>
      <h3>No doctors listed yet</h3>
      <p>Please check back soon.</p>
    </div>
  <?php else: ?>
    <div class="doc-grid">
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="doc-card a1">
          <div class="doc-card-top">
            <div class="doc-avatar">👨‍⚕️</div>
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p class="sp"><?php echo htmlspecialchars($row['specialty']); ?></p>
          </div>
          <div class="doc-card-bot">
            <span class="doc-phone"><?php echo htmlspecialchars($row['phone']); ?></span>
            <a href="book_appointment.php?doctor_id=<?php echo (int)$row['id']; ?>" class="btn btn-navy" style="font-size:.78rem;padding:7px 16px;">Book →</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
