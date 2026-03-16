<?php
session_start();
include "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass  = $_POST['password'];
    $pass2 = $_POST['password2'];

    if (empty($name) || empty($email) || empty($pass)) {
        $error = "Name, email and password are required.";
    } elseif ($pass !== $pass2) {
        $error = "Passwords do not match.";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM patients WHERE email='$email_safe' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = "An account with that email already exists.";
        } else {
            $hashed     = password_hash($pass, PASSWORD_DEFAULT);
            $name_safe  = mysqli_real_escape_string($conn, $name);
            $phone_safe = mysqli_real_escape_string($conn, $phone);
            $sql = "INSERT INTO patients (name, email, password, phone)
                    VALUES ('$name_safe', '$email_safe', '$hashed', '$phone_safe')";
            if (mysqli_query($conn, $sql)) {
                $success = "Account created! <a href='login.php?type=patient'>Click here to log in →</a>";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
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
<title>Patient Registration – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="banner">
  <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=1200&auto=format&fit=crop&q=70" alt="Register">
  <h2>🧑‍⚕️ Patient Registration</h2>
</div>

<div class="wrap">

  <?php if ($success): ?>
    <div class="alert alert-ok"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-err"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="form-box a1">
    <form method="POST" action="">

      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required placeholder="Your full name"
        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">

      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required placeholder="you@example.com"
        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

      <label for="phone">Phone Number</label>
      <input type="text" id="phone" name="phone" placeholder="+1 (555) 000-0000"
        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="At least 6 characters">

      <label for="password2">Confirm Password</label>
      <input type="password" id="password2" name="password2" required placeholder="Repeat password">

      <button type="submit" class="submit-btn">Create Account →</button>

    </form>
    <p style="margin-top:20px;font-size:.85rem;color:var(--text3)">
      Already registered? <a href="login.php?type=patient">Log in here</a>
    </p>
  </div>
</div>

</body>
</html>
