<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?type=patient");
    exit;
}
include "config.php";

$uid = (int)$_SESSION['user_id'];

$sql = "SELECT a.id, a.appointment_date, a.message, a.status,
               d.name AS doctor_name, d.specialty
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = $uid
        ORDER BY a.appointment_date DESC";

$result = mysqli_query($conn, $sql);

$upcoming = [];
$past     = [];
$today    = date('Y-m-d');

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['appointment_date'] >= $today) {
        $upcoming[] = $row;
    } else {
        $past[] = $row;
    }
}
$total = count($upcoming) + count($past);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Appointments – Homeland Hospital</title>
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
  <img src="https://images.unsplash.com/photo-1504439468489-c8920d796a29?w=1200&auto=format&fit=crop&q=70" alt="Appointments">
  <h2>🗒 My Appointments</h2>
</div>

<div class="wrap">

  <?php if ($total === 0): ?>
    <div class="empty">
      <span class="eic">📅</span>
      <h3>No appointments yet</h3>
      <p>You haven't booked any visits. Get started now.</p>
      <a href="book_appointment.php" class="btn btn-navy">Book an Appointment</a>
    </div>

  <?php else: ?>

    <div class="sch-head">
      <div>
        <h2 class="page-title"><?php echo htmlspecialchars($_SESSION['user_name']); ?>'s Visits</h2>
      </div>
      <span class="stat-chip">
        <strong><?php echo count($upcoming); ?></strong> upcoming &nbsp;·&nbsp;
        <strong><?php echo count($past); ?></strong> past
      </span>
    </div>

    <?php if (count($upcoming) > 0): ?>
      <p class="sec-label">Upcoming</p>
      <div class="tbl-wrap a1" style="margin-bottom:32px">
        <table>
          <thead>
            <tr><th>Doctor</th><th>Specialty</th><th>Date</th><th>Status</th><th>Reason</th></tr>
          </thead>
          <tbody>
          <?php foreach ($upcoming as $row): ?>
            <tr>
              <td><strong>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></strong></td>
              <td><span class="badge"><?php echo htmlspecialchars($row['specialty']); ?></span></td>
              <td><?php echo date('D, d M Y', strtotime($row['appointment_date'])); ?></td>
              <td><span class="badge" style="background:rgba(42,157,143,.12);color:var(--green)"><?php echo ucfirst($row['status']); ?></span></td>
              <td style="color:var(--text3);font-size:.83rem"><?php echo $row['message'] ? htmlspecialchars($row['message']) : '—'; ?></td>
              <td>
    <span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
    <?php if ($row['appointment_date'] >= $today && in_array($row['status'], ['pending', 'confirmed', 'rescheduled'])): ?>
        <div style="display: flex; gap: 5px; margin-top: 5px;">
            <a href="chat.php?appointment_id=<?php echo $row['id']; ?>" class="tbl-link" style="padding: 3px 8px; font-size: 0.7rem;">💬 Chat</a>
            <a href="appointment_action.php?id=<?php echo $row['id']; ?>&action=reschedule" class="tbl-link" style="padding: 3px 8px; font-size: 0.7rem; background: var(--warning);">📅 Reschedule</a>
            <a href="appointment_action.php?id=<?php echo $row['id']; ?>&action=cancel" class="tbl-link" style="padding: 3px 8px; font-size: 0.7rem; background: var(--danger);">❌ Cancel</a>
        </div>
    <?php endif; ?>
</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <?php if (count($past) > 0): ?>
      <p class="sec-label past">Past Visits</p>
      <div class="tbl-wrap a2">
        <table>
          <thead>
            <tr><th>Doctor</th><th>Specialty</th><th>Date</th><th>Status</th><th>Reason</th></tr>
          </thead>
          <tbody>
          <?php foreach ($past as $row): ?>
            <tr style="opacity:.65">
              <td><strong>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></strong></td>
              <td><span class="badge"><?php echo htmlspecialchars($row['specialty']); ?></span></td>
              <td><?php echo date('D, d M Y', strtotime($row['appointment_date'])); ?></td>
              <td><span class="badge"><?php echo ucfirst($row['status']); ?></span></td>
              <td style="color:var(--text3);font-size:.83rem"><?php echo $row['message'] ? htmlspecialchars($row['message']) : '—'; ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</div>
</body>
</html>
