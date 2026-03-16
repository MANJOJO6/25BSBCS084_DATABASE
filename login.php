<?php
session_start();
include "config.php";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Validate type param
$type = isset($_GET['type']) ? $_GET['type'] : '';
if ($type !== 'patient' && $type !== 'doctor') {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $table    = ($type === 'patient') ? 'patients' : 'doctors';

    $email_safe = mysqli_real_escape_string($conn, $email);
    $sql        = "SELECT * FROM `$table` WHERE email = '$email_safe' LIMIT 1";
    $result     = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row   = mysqli_fetch_assoc($result);
        $valid = false;

        // Support bcrypt hashed passwords AND plain-text passwords
        if (password_verify($password, $row['password'])) {
            $valid = true;
        } elseif ($password === $row['password']) {
            $valid = true; // plain-text (from SQL import)
        }

        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_type'] = $type;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}

$banner = ($type === 'doctor')
    ? "https://images.unsplash.com/photo-1666214280557-f1b5022eb634?w=1200&auto=format&fit=crop&q=70"
    : "https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=1200&auto=format&fit=crop&q=70";
$heading = ($type === 'doctor') ? '👨‍⚕️ Doctor Login' : '🧑‍⚕️ Patient Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo ucfirst($type); ?> Login – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="banner">
  <img src="<?php echo $banner; ?>" alt="Login">
  <h2><?php echo $heading; ?></h2>
</div>

<div class="wrap">

  <?php if ($error): ?>
    <div class="alert alert-err"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="form-box a1">

    <form method="POST" action="">

      <label for="email">Email Address</label>
      <input
        type="email" id="email" name="email" required
        placeholder="you@example.com"
        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="••••••••">

      <button type="submit" class="submit-btn">Sign In →</button>

    </form>

    <p style="margin-top:22px;font-size:.85rem;color:var(--text3)">
      <a href="index.php">← Back to home</a>
      <?php if ($type === 'patient'): ?>
        &nbsp;|&nbsp; <a href="register.php">Register as patient</a>
      <?php endif; ?>
    </p>

  </div>
</div>

</body>
</html>
