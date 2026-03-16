<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include "config.php";

$user_id = (int)$_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (!$appointment_id || !in_array($action, ['cancel', 'reschedule'])) {
    header("Location: " . ($user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'));
    exit;
}

// Verify appointment belongs to user
$verify_sql = "SELECT a.*, 
               p.name as patient_name, p.email as patient_email,
               d.name as doctor_name, d.email as doctor_email
               FROM appointments a
               JOIN patients p ON a.patient_id = p.id
               JOIN doctors d ON a.doctor_id = d.id
               WHERE a.id = $appointment_id";

if ($user_type === 'patient') {
    $verify_sql .= " AND a.patient_id = $user_id";
} else {
    $verify_sql .= " AND a.doctor_id = $user_id";
}

$result = mysqli_query($conn, $verify_sql);
$appointment = mysqli_fetch_assoc($result);

if (!$appointment) {
    header("Location: " . ($user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'));
    exit;
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'cancel') {
        $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));
        
        // Update appointment status
        $update_sql = "UPDATE appointments SET status = 'cancelled' WHERE id = $appointment_id";
        
        if (mysqli_query($conn, $update_sql)) {
            // Log to history
            $history_sql = "INSERT INTO appointment_history (appointment_id, action, changed_by, user_id, reason) 
                           VALUES ($appointment_id, 'cancelled', '$user_type', $user_id, '$reason')";
            mysqli_query($conn, $history_sql);
            
            // Create notification for the other party
            $other_user_id = ($user_type === 'patient') ? $appointment['doctor_id'] : $appointment['patient_id'];
            $other_user_type = ($user_type === 'patient') ? 'doctor' : 'patient';
            $other_name = ($user_type === 'patient') ? $appointment['doctor_name'] : $appointment['patient_name'];
            
            $notify_sql = "INSERT INTO notifications (user_id, user_type, type, title, message, reference_id, reference_type) 
                          VALUES ($other_user_id, '$other_user_type', 'cancellation', 
                          'Appointment Cancelled', 
                          'Your appointment with " . ($user_type === 'patient' ? 'Dr. ' . $appointment['doctor_name'] : $appointment['patient_name']) . " on " . date('M d, Y', strtotime($appointment['appointment_date'])) . " has been cancelled. Reason: $reason', 
                          $appointment_id, 'appointment')";
            mysqli_query($conn, $notify_sql);
            
            $success = "Appointment cancelled successfully!";
        } else {
            $error = "Failed to cancel appointment.";
        }
    } elseif ($action === 'reschedule') {
        $new_date = $_POST['new_date'];
        $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));
        
        if (empty($new_date) || $new_date < date('Y-m-d')) {
            $error = "Please select a valid future date.";
        } else {
            $old_date = $appointment['appointment_date'];
            
            // Update appointment
            $update_sql = "UPDATE appointments SET appointment_date = '$new_date', status = 'rescheduled' WHERE id = $appointment_id";
            
            if (mysqli_query($conn, $update_sql)) {
                // Log to history
                $history_sql = "INSERT INTO appointment_history (appointment_id, action, changed_by, user_id, old_date, new_date, reason) 
                               VALUES ($appointment_id, 'rescheduled', '$user_type', $user_id, '$old_date', '$new_date', '$reason')";
                mysqli_query($conn, $history_sql);
                
                // Create notification
                $other_user_id = ($user_type === 'patient') ? $appointment['doctor_id'] : $appointment['patient_id'];
                $other_user_type = ($user_type === 'patient') ? 'doctor' : 'patient';
                
                $notify_sql = "INSERT INTO notifications (user_id, user_type, type, title, message, reference_id, reference_type) 
                              VALUES ($other_user_id, '$other_user_type', 'reschedule', 
                              'Appointment Rescheduled', 
                              'Your appointment has been rescheduled from " . date('M d, Y', strtotime($old_date)) . " to " . date('M d, Y', strtotime($new_date)) . ". Reason: $reason', 
                              $appointment_id, 'appointment')";
                mysqli_query($conn, $notify_sql);
                
                $success = "Appointment rescheduled successfully!";
            } else {
                $error = "Failed to reschedule appointment.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo ucfirst($action); ?> Appointment – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%232563eb'/%3E%3Cpath d='M20 50 L40 30 L60 30 L80 50 L70 60 L50 40 L30 60 L20 50Z' fill='white'/%3E%3Ccircle cx='35' cy='45' r='5' fill='%23f59e0b'/%3E%3Ccircle cx='65' cy='45' r='5' fill='%23f59e0b'/%3E%3C/svg%3E">
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
  <a href="notifications.php" class="nav-notification">🔔 Notifications</a>
  <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<div class="banner">
  <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1200&auto=format&fit=crop&q=70" alt="Action">
  <h2><?php echo $action === 'cancel' ? '❌ Cancel Appointment' : '📅 Reschedule Appointment'; ?></h2>
</div>

<div class="wrap">
  <div class="form-box a1" style="max-width: 600px;">
    
    <?php if ($success): ?>
      <div class="alert alert-ok"><?php echo $success; ?></div>
      <div style="text-align: center; margin-top: 20px;">
        <a href="<?php echo $user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'; ?>" class="btn btn-primary">← Back to Appointments</a>
        <a href="chat.php?appointment_id=<?php echo $appointment_id; ?>" class="btn btn-success">💬 Open Chat</a>
      </div>
    <?php else: ?>
      <?php if ($error): ?>
        <div class="alert alert-err"><?php echo $error; ?></div>
      <?php endif; ?>

      <div class="appointment-summary" style="background: var(--gray-50); padding: 20px; border-radius: var(--radius); margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: var(--gray-700);">Appointment Details</h3>
        <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
        <p><strong>Current Date:</strong> <?php echo date('l, F j, Y', strtotime($appointment['appointment_date'])); ?></p>
        <p><strong>Status:</strong> <span class="badge badge-<?php echo $appointment['status']; ?>"><?php echo ucfirst($appointment['status']); ?></span></p>
      </div>

      <form method="POST" action="">
        <?php if ($action === 'cancel'): ?>
          <label for="reason">Reason for Cancellation</label>
          <textarea id="reason" name="reason" required placeholder="Please provide a reason for cancelling..." rows="4"></textarea>
          <p style="color: var(--gray-500); font-size: 0.85rem; margin-top: 5px;">This will be shared with <?php echo $user_type === 'patient' ? 'Dr. ' . $appointment['doctor_name'] : $appointment['patient_name']; ?></p>
          
          <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-danger" style="flex: 1;">Confirm Cancellation</button>
            <a href="<?php echo $user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'; ?>" class="btn btn-outline" style="flex: 1;">Go Back</a>
          </div>

        <?php elseif ($action === 'reschedule'): ?>
          <label for="new_date">New Preferred Date</label>
          <input type="date" id="new_date" name="new_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">

          <label for="reason" style="margin-top: 20px;">Reason for Rescheduling</label>
          <textarea id="reason" name="reason" required placeholder="Please provide a reason for rescheduling..." rows="4"></textarea>

          <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Confirm Reschedule</button>
            <a href="<?php echo $user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'; ?>" class="btn btn-outline" style="flex: 1;">Go Back</a>
          </div>
        <?php endif; ?>
      </form>
    <?php endif; ?>
  </div>
</div>

<style>
.appointment-summary p {
  margin-bottom: 8px;
  color: var(--gray-700);
}
.badge-cancelled { background: var(--danger-light); color: #991b1b; }
.badge-confirmed { background: var(--success-light); color: #065f46; }
.badge-pending { background: var(--warning-light); color: #92400e; }
.badge-rescheduled { background: var(--primary-soft); color: var(--primary-dark); }
</style>

</body>
</html>