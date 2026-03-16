<?php
$host = "localhost";   // Change to "localhost" if connecting to local MySQL
$user = "root";
$pass = "";          // Change if your MySQL root has a password
$db   = "homeland_hospital";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<h2 style='font-family:sans-serif;color:red;padding:40px'>
        Database Error: " . mysqli_connect_error() . "<br><br>
        <small>Make sure:<br>
        1. XAMPP/WAMP MySQL is running<br>
        2. You imported homeland_hospital.sql<br>
        3. Database name is <b>homeland_hospital</b></small>
    </h2>");
}

mysqli_set_charset($conn, "utf8mb4");
