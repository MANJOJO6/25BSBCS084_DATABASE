<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?type=patient");
    exit;
}
if ($_SESSION['user_type'] !== 'patient') {
    header("Location: dashboard.php");
    exit;
}
include "config.php";

$success   = '';
$error_msg = '';
$preselect = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

// Load doctors
$doc_result = mysqli_query($conn, "SELECT id, name, specialty, phone FROM doctors ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = (int)$_SESSION['user_id'];
    $doctor_id  = (int)$_POST['doctor_id'];
    $date       = trim($_POST['date']);
    $message    = trim($_POST['message']);

    if ($doctor_id <= 0) {
        $error_msg = "Please select a doctor.";
    } elseif (empty($date) || $date < date('Y-m-d')) {
        $error_msg = "Please select a valid future date.";
    } else {
        $date_safe    = mysqli_real_escape_string($conn, $date);
        $message_safe = mysqli_real_escape_string($conn, $message);

        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, message)
                VALUES ('$patient_id', '$doctor_id', '$date_safe', '$message_safe')";

        if (mysqli_query($conn, $sql)) {
            $success = "Appointment booked successfully! <a href='my_appointments.php'>View your appointments →</a>";
        } else {
            $error_msg = "Could not book appointment: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment – Homeland Hospital</title>
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
  <img src="https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=1200&auto=format&fit=crop&q=70" alt="Book">
  <h2>📅 Book an Appointment</h2>
</div>

<div class="wrap">
  <h2 class="page-title">Schedule a Visit</h2>
  <p class="page-sub">Choose a doctor, pick a date, and describe your concern.</p>

  <?php if ($success): ?>
    <div class="alert alert-ok"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-err"><?php echo htmlspecialchars($error_msg); ?></div>
  <?php endif; ?>

  <div class="appt-grid">

    <!-- Form -->
    <div class="form-box a1">
      <form method="POST" action="">

        <label for="doctor_id">Select Doctor</label>
        <select id="doctor_id" name="doctor_id" required>
          <option value="">— Choose a doctor —</option>
          <?php while ($doc = mysqli_fetch_assoc($doc_result)): ?>
            <option value="<?php echo (int)$doc['id']; ?>"
              <?php echo ($doc['id'] == $preselect) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($doc['name']); ?> — <?php echo htmlspecialchars($doc['specialty']); ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label for="date">Preferred Date</label>
        <input type="date" id="date" name="date" required
          min="<?php echo date('Y-m-d'); ?>"
          value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">

        <label for="message">Reason for Visit</label>
        <textarea id="message" name="message" placeholder="Briefly describe your symptoms or concern..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>

        <button type="submit" class="submit-btn">Confirm Appointment →</button>

      </form>
    </div>

    <!-- Info sidebar -->
    <div class="info-box a2">
      <h3>Appointment Info</h3>
      <div class="info-row">
        <span class="ri">🏥</span>
        <div><strong>Hospital</strong><span>Homeland Hospital</span></div>
      </div>
      <div class="info-row">
        <span class="ri">⏰</span>
        <div><strong>Working Hours</strong><span>Mon – Fri: 8:00 AM – 6:00 PM</span></div>
      </div>
      <div class="info-row">
        <span class="ri">📞</span>
        <div><strong>Emergency</strong><span>+1 (800) 555-HLND</span></div>
      </div>
      <div class="info-row">
        <span class="ri">📋</span>
        <div><strong>What to bring</strong><span>Valid ID, insurance card, and any prior medical records.</span></div>
      </div>
      <div class="info-row">
        <span class="ri">ℹ️</span>
        <div><strong>Note</strong><span>Appointments are subject to doctor availability. You will be contacted to confirm.</span></div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
