<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include "config.php";

$user_id = (int)$_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

if (!$appointment_id) {
    header("Location: " . ($user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'));
    exit;
}

// Verify user has access to this appointment
$verify_sql = "SELECT a.*, 
               p.name as patient_name, p.id as patient_id,
               d.name as doctor_name, d.id as doctor_id
               FROM appointments a
               JOIN patients p ON a.patient_id = p.id
               JOIN doctors d ON a.doctor_id = d.id
               WHERE a.id = $appointment_id";

$result = mysqli_query($conn, $verify_sql);
$appointment = mysqli_fetch_assoc($result);

if (!$appointment || 
    ($user_type === 'patient' && $appointment['patient_id'] != $user_id) ||
    ($user_type === 'doctor' && $appointment['doctor_id'] != $user_id)) {
    header("Location: " . ($user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'));
    exit;
}

// Handle new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    if (!empty($message)) {
        $insert_sql = "INSERT INTO chat_messages (appointment_id, sender_id, sender_type, message) 
                       VALUES ($appointment_id, $user_id, '$user_type', '$message')";
        mysqli_query($conn, $insert_sql);
        
        // Create notification for the other party
        $other_user_id = ($user_type === 'patient') ? $appointment['doctor_id'] : $appointment['patient_id'];
        $other_user_type = ($user_type === 'patient') ? 'doctor' : 'patient';
        
        $notify_sql = "INSERT INTO notifications (user_id, user_type, type, title, message, reference_id, reference_type) 
                      VALUES ($other_user_id, '$other_user_type', 'chat', 
                      'New Message', 
                      'You have a new message regarding your appointment on " . date('M d, Y', strtotime($appointment['appointment_date'])) . "', 
                      $appointment_id, 'chat')";
        mysqli_query($conn, $notify_sql);
    }
    
    // Redirect to avoid form resubmission
    header("Location: chat.php?appointment_id=$appointment_id");
    exit;
}

// Fetch messages
$messages_sql = "SELECT cm.*, 
                 CASE 
                   WHEN cm.sender_type = 'patient' THEN p.name
                   ELSE d.name
                 END as sender_name
                 FROM chat_messages cm
                 LEFT JOIN patients p ON cm.sender_type = 'patient' AND cm.sender_id = p.id
                 LEFT JOIN doctors d ON cm.sender_type = 'doctor' AND cm.sender_id = d.id
                 WHERE cm.appointment_id = $appointment_id
                 ORDER BY cm.created_at ASC";

$messages_result = mysqli_query($conn, $messages_sql);

// Mark messages as read
$update_read_sql = "UPDATE chat_messages 
                    SET is_read = 1, read_at = NOW() 
                    WHERE appointment_id = $appointment_id 
                    AND sender_id != $user_id 
                    AND is_read = 0";
mysqli_query($conn, $update_read_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%232563eb'/%3E%3Cpath d='M20 50 L40 30 L60 30 L80 50 L70 60 L50 40 L30 60 L20 50Z' fill='white'/%3E%3Ccircle cx='35' cy='45' r='5' fill='%23f59e0b'/%3E%3Ccircle cx='65' cy='45' r='5' fill='%23f59e0b'/%3E%3C/svg%3E">
<style>
.chat-container {
  background: var(--white);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  overflow: hidden;
  height: 600px;
  display: flex;
  flex-direction: column;
}

.chat-header {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  color: var(--white);
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.chat-header-info h2 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.chat-header-info p {
  font-size: 0.85rem;
  opacity: 0.9;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
  background: var(--gray-50);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.message {
  display: flex;
  flex-direction: column;
  max-width: 70%;
}

.message-sent {
  align-self: flex-end;
}

.message-received {
  align-self: flex-start;
}

.message-bubble {
  padding: 12px 16px;
  border-radius: 18px;
  font-size: 0.95rem;
  line-height: 1.5;
  word-wrap: break-word;
}

.message-sent .message-bubble {
  background: var(--primary);
  color: var(--white);
  border-bottom-right-radius: 4px;
}

.message-received .message-bubble {
  background: var(--white);
  color: var(--gray-800);
  border: 1px solid var(--gray-200);
  border-bottom-left-radius: 4px;
}

.message-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
  font-size: 0.7rem;
  color: var(--gray-500);
  padding: 0 8px;
}

.message-sent .message-info {
  justify-content: flex-end;
}

.message-status {
  font-size: 0.7rem;
  color: var(--gray-400);
}

.chat-input-area {
  padding: 20px 24px;
  background: var(--white);
  border-top: 2px solid var(--gray-200);
}

.chat-form {
  display: flex;
  gap: 12px;
}

.chat-input {
  flex: 1;
  padding: 12px 16px;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius);
  font-size: 0.95rem;
  transition: all 0.2s;
}

.chat-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px var(--primary-soft);
}

.chat-send-btn {
  padding: 12px 28px;
  background: var(--primary);
  color: var(--white);
  border: none;
  border-radius: var(--radius);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.chat-send-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--gray-600);
  text-decoration: none;
  font-size: 0.9rem;
  margin-bottom: 16px;
  font-weight: 500;
}

.back-link:hover {
  color: var(--primary);
}

.typing-indicator {
  display: none;
  padding: 8px 16px;
  color: var(--gray-500);
  font-size: 0.85rem;
  font-style: italic;
}

.chat-actions {
  display: flex;
  gap: 12px;
  margin-left: auto;
}

.chat-action-btn {
  background: rgba(255,255,255,0.2);
  border: none;
  color: var(--white);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.chat-action-btn:hover {
  background: rgba(255,255,255,0.3);
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
  <a href="notifications.php" class="nav-notification">🔔 Notifications</a>
  <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<div class="wrap" style="max-width: 900px;">
  <a href="<?php echo $user_type === 'patient' ? 'my_appointments.php' : 'doctor_appointments.php'; ?>" class="back-link">
    ← Back to Appointments
  </a>

  <div class="chat-container a1">
    <div class="chat-header">
      <div style="font-size: 2rem;">💬</div>
      <div class="chat-header-info">
        <h2>Chat with <?php echo $user_type === 'patient' ? 'Dr. ' . $appointment['doctor_name'] : $appointment['patient_name']; ?></h2>
        <p>Appointment: <?php echo date('l, F j, Y', strtotime($appointment['appointment_date'])); ?></p>
      </div>
      <div class="chat-actions">
        <a href="appointment_action.php?id=<?php echo $appointment_id; ?>&action=reschedule" class="chat-action-btn">📅 Reschedule</a>
        <a href="appointment_action.php?id=<?php echo $appointment_id; ?>&action=cancel" class="chat-action-btn">❌ Cancel</a>
      </div>
    </div>

    <div class="chat-messages" id="chatMessages">
      <?php 
      $last_date = '';
      while ($msg = mysqli_fetch_assoc($messages_result)): 
        $msg_date = date('Y-m-d', strtotime($msg['created_at']));
        if ($last_date != $msg_date):
          $last_date = $msg_date;
      ?>
        <div style="text-align: center; margin: 10px 0;">
          <span style="background: var(--gray-200); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: var(--gray-600);">
            <?php echo date('F j, Y', strtotime($msg_date)); ?>
          </span>
        </div>
      <?php endif; ?>
      
      <div class="message <?php echo $msg['sender_id'] == $user_id && $msg['sender_type'] == $user_type ? 'message-sent' : 'message-received'; ?>">
        <div class="message-bubble">
          <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <div class="message-info">
          <span><?php echo date('h:i A', strtotime($msg['created_at'])); ?></span>
          <?php if ($msg['sender_id'] == $user_id && $msg['sender_type'] == $user_type): ?>
            <span class="message-status">
              <?php echo $msg['is_read'] ? '✓✓ Read' : '✓ Sent'; ?>
            </span>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <div class="typing-indicator" id="typingIndicator">
      <?php echo $user_type === 'patient' ? 'Dr. ' . $appointment['doctor_name'] : $appointment['patient_name']; ?> is typing...
    </div>

    <div class="chat-input-area">
      <form class="chat-form" method="POST" action="" id="chatForm">
        <input type="text" 
               name="message" 
               class="chat-input" 
               placeholder="Type your message..." 
               autocomplete="off"
               id="messageInput"
               required>
        <button type="submit" class="chat-send-btn">
          <span>Send</span>
          <span>→</span>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
// Auto-scroll to bottom
const chatMessages = document.getElementById('chatMessages');
chatMessages.scrollTop = chatMessages.scrollHeight;

// Auto-refresh messages every 3 seconds
setInterval(function() {
  fetch('get_messages.php?appointment_id=<?php echo $appointment_id; ?>')
    .then(response => response.text())
    .then(data => {
      chatMessages.innerHTML = data;
      chatMessages.scrollTop = chatMessages.scrollHeight;
    });
}, 3000);

// Typing indicator
let typingTimer;
const messageInput = document.getElementById('messageInput');
const typingIndicator = document.getElementById('typingIndicator');

messageInput.addEventListener('keypress', function() {
  clearTimeout(typingTimer);
  fetch('typing_indicator.php?appointment_id=<?php echo $appointment_id; ?>&typing=1');
  
  typingTimer = setTimeout(function() {
    fetch('typing_indicator.php?appointment_id=<?php echo $appointment_id; ?>&typing=0');
  }, 1000);
});
</script>

</body>
</html>