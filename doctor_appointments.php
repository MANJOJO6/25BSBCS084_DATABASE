<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
if ($_SESSION['user_type'] !== 'doctor') {
    header("Location: dashboard.php");
    exit;
}
include "config.php";

$doctor_id = (int)$_SESSION['user_id'];
$doctor_name = htmlspecialchars($_SESSION['user_name']);

// Fetch appointments for this doctor
$sql = "SELECT 
            a.id,
            a.appointment_date, 
            a.message, 
            a.status,
            p.id AS patient_id,
            p.name AS patient_name, 
            p.email AS patient_email, 
            p.phone AS patient_phone
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = $doctor_id
        ORDER BY a.appointment_date DESC";

$result = mysqli_query($conn, $sql);

// Separate upcoming and past appointments
$upcoming = [];
$past = [];
$today = date('Y-m-d');

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['appointment_date'] >= $today) {
            $upcoming[] = $row;
        } else {
            $past[] = $row;
        }
    }
}

$total_appointments = count($upcoming) + count($past);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule – Homeland Hospital</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%232563eb'/%3E%3Cpath d='M20 50 L40 30 L60 30 L80 50 L70 60 L50 40 L30 60 L20 50Z' fill='white'/%3E%3Ccircle cx='35' cy='45' r='5' fill='%23f59e0b'/%3E%3Ccircle cx='65' cy='45' r='5' fill='%23f59e0b'/%3E%3C/svg%3E">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            color: white !important;
            transition: all 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        .btn-chat { background: var(--primary); }
        .btn-reschedule { background: var(--warning); }
        btn-cancel { background: var(--danger); }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-pending { background: var(--warning-light); color: #92400e; }
        .status-confirmed { background: var(--success-light); color: #065f46; }
        .status-cancelled { background: var(--danger-light); color: #991b1b; }
        .status-completed { background: var(--gray-200); color: var(--gray-700); }
        .status-rescheduled { background: var(--primary-soft); color: var(--primary-dark); }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav>
    <span class="nav-brand">🏥 Homeland Hospital</span>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="doctor_appointments.php" class="active">🗒 My Schedule</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="logout.php" class="nav-logout">🚪 Logout</a>
</nav>

<!-- Banner Section -->
<div class="banner">
    <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=1200&auto=format&fit=crop&q=70" alt="Schedule">
    <h2>🗒 Dr. <?php echo $doctor_name; ?>'s Schedule</h2>
</div>

<!-- Main Content -->
<div class="wrap">

    <?php if ($total_appointments === 0): ?>
        <!-- Empty State -->
        <div class="empty">
            <span class="eic">📋</span>
            <h3>No appointments yet</h3>
            <p>No patients have booked appointments with you yet.</p>
            <p style="margin-top: 20px; color: var(--gray-500);">Check back later or update your availability.</p>
        </div>

    <?php else: ?>

        <!-- Stats Header -->
        <div class="sch-head">
            <div>
                <h2 class="page-title">My Patient Schedule</h2>
                <p class="page-sub">Manage your upcoming and past appointments</p>
            </div>
            <div class="stat-chip">
                <strong><?php echo count($upcoming); ?></strong> Upcoming 
                <span style="margin: 0 8px; color: var(--gray-300);">|</span>
                <strong><?php echo count($past); ?></strong> Past
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <?php if (count($upcoming) > 0): ?>
            <div class="section-header">
                <h3 class="sec-label">📅 Upcoming Appointments</h3>
                <p class="section-desc">Appointments scheduled for today and future dates</p>
            </div>
            
            <div class="tbl-wrap a1">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $row): ?>
                        <tr>
                            <!-- Patient Name -->
                            <td>
                                <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong>
                            </td>
                            
                            <!-- Contact Info -->
                            <td>
                                <div style="font-size: 0.8rem;">
                                    <div>📧 <a href="mailto:<?php echo htmlspecialchars($row['patient_email']); ?>" style="color: var(--primary);">
                                        <?php echo htmlspecialchars($row['patient_email']); ?>
                                    </a></div>
                                    <?php if (!empty($row['patient_phone'])): ?>
                                        <div style="margin-top: 3px;">📞 <?php echo htmlspecialchars($row['patient_phone']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <!-- Date -->
                            <td>
                                <strong><?php echo date('D, M d, Y', strtotime($row['appointment_date'])); ?></strong>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            
                            <!-- Reason -->
                            <td>
                                <?php if (!empty($row['message'])): ?>
                                    <div style="max-width: 200px; font-size: 0.8rem; color: var(--gray-600);">
                                        "<?php echo htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : ''); ?>"
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--gray-400);">—</span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Action Buttons -->
                            <td>
                                <?php if (in_array($row['status'], ['pending', 'confirmed', 'rescheduled'])): ?>
                                    <div class="action-buttons">
                                        <a href="chat.php?appointment_id=<?php echo $row['id']; ?>" 
                                           class="action-btn btn-chat" 
                                           title="Chat with patient">
                                            💬 Chat
                                        </a>
                                        <a href="appointment_action.php?id=<?php echo $row['id']; ?>&action=reschedule" 
                                           class="action-btn btn-reschedule" 
                                           title="Reschedule appointment">
                                            📅 Reschedule
                                        </a>
                                        <a href="appointment_action.php?id=<?php echo $row['id']; ?>&action=cancel" 
                                           class="action-btn btn-cancel" 
                                           title="Cancel appointment"
                                           onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                            ❌ Cancel
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--gray-400); font-size: 0.8rem;">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Past Appointments -->
        <?php if (count($past) > 0): ?>
            <div style="margin-top: 40px;">
                <div class="section-header">
                    <h3 class="sec-label past">📋 Past Appointments</h3>
                    <p class="section-desc">Completed and historical appointments</p>
                </div>
                
                <div class="tbl-wrap a2">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($past as $row): ?>
                            <tr style="opacity: 0.8;">
                                <!-- Patient Name -->
                                <td>
                                    <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong>
                                </td>
                                
                                <!-- Contact Info -->
                                <td>
                                    <div style="font-size: 0.8rem;">
                                        <div>📧 <?php echo htmlspecialchars($row['patient_email']); ?></div>
                                        <?php if (!empty($row['patient_phone'])): ?>
                                            <div>📞 <?php echo htmlspecialchars($row['patient_phone']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <!-- Date -->
                                <td>
                                    <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?>
                                </td>
                                
                                <!-- Status -->
                                <td>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                
                                <!-- Reason -->
                                <td>
                                    <?php if (!empty($row['message'])): ?>
                                        <div style="font-size: 0.8rem; color: var(--gray-600);">
                                            <?php echo htmlspecialchars($row['message']); ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--gray-400);">—</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Notes -->
                                <td>
                                    <a href="chat.php?appointment_id=<?php echo $row['id']; ?>" 
                                       class="action-btn btn-chat" 
                                       style="padding: 2px 6px; font-size: 0.65rem;">
                                        View History
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Stats Summary -->
        <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="background: var(--white); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
                <div style="font-size: 2rem; color: var(--primary);">📊</div>
                <h4 style="margin: 10px 0; color: var(--gray-700);">Total Appointments</h4>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?php echo $total_appointments; ?></p>
            </div>
            
            <div style="background: var(--white); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
                <div style="font-size: 2rem; color: var(--success);">✅</div>
                <h4 style="margin: 10px 0; color: var(--gray-700);">Completed</h4>
                <p style="font-size: 2rem; font-weight: 700; color: var(--success);">
                    <?php 
                    $completed = array_filter($past, function($a) { return $a['status'] === 'completed'; });
                    echo count($completed);
                    ?>
                </p>
            </div>
            
            <div style="background: var(--white); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
                <div style="font-size: 2rem; color: var(--warning);">⏳</div>
                <h4 style="margin: 10px 0; color: var(--gray-700);">Pending</h4>
                <p style="font-size: 2rem; font-weight: 700; color: var(--warning);">
                    <?php 
                    $pending = array_filter($upcoming, function($a) { return $a['status'] === 'pending'; });
                    echo count($pending);
                    ?>
                </p>
            </div>
        </div>

    <?php endif; ?>

</div>

<!-- Optional JavaScript for Confirmations -->
<script>
function confirmCancel(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
        window.location.href = 'appointment_action.php?id=' + appointmentId + '&action=cancel';
    }
    return false;
}
</script>

</body>
</html>