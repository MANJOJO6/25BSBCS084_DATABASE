<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit;
}
include "config.php";

$user_id = (int)$_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

if (!$appointment_id) {
    exit;
}

// Verify access
$verify_sql = "SELECT id FROM appointments 
               WHERE id = $appointment_id 
               AND (patient_id = $user_id OR doctor_id = $user_id)";
$result = mysqli_query($conn, $verify_sql);

if (mysqli_num_rows($result) === 0) {
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