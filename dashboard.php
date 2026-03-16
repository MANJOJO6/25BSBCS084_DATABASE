<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$name = $_SESSION['user_name'];
$type = $_SESSION['user_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
  <span class="nav-brand">🏥 Homeland</span>
  <a href="dashboard.php">🏠 Dashboard</a>
  <?php if ($type === 'patient'): ?>
    <a href="doctors.php">👨‍⚕️ Doctors</a>
    <a href="book_appointment.php">📅 Book</a>
    <a href="my_appointments.php">🗒 My Appointments</a>
  <?php else: ?>
    <a href="doctor_appointments.php">🗒 My Schedule</a>
  <?php endif; ?>
  <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<div class="wrap">

  <div class="welcome a1">
    <h2>Welcome back, <?php echo htmlspecialchars($name); ?>!</h2>
    <p>Here's your portal overview.</p>
    <span class="role-tag"><?php echo ucfirst($type); ?></span>
  </div>

  <div class="cards">

    <?php if ($type === 'patient'): ?>

      <div class="card a1">
        <span class="ic">👨‍⚕️</span>
        <h3>Our Doctors</h3>
        <small>Browse our team of specialists</small>
        <a href="doctors.php" class="btn btn-navy">Browse Doctors</a>
      </div>

      <div class="card a2">
        <span class="ic">📅</span>
        <h3>Book a Visit</h3>
        <small>Schedule a new appointment</small>
        <a href="book_appointment.php" class="btn btn-teal">Book Now</a>
      </div>

      <div class="card a3">
        <span class="ic">🗒</span>
        <h3>My Appointments</h3>
        <small>View your upcoming visits</small>
        <a href="my_appointments.php" class="btn btn-green">View All</a>
      </div>

    <?php else: ?>

      <div class="card a1">
        <span class="ic">🗒</span>
        <h3>Patient Schedule</h3>
        <small>See patients booked with you</small>
        <a href="doctor_appointments.php" class="btn btn-navy">View Schedule</a>
      </div>

    <?php endif; ?>

  </div>
</div>

</body>
</html>
