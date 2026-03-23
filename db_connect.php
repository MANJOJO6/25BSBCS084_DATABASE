<?php
/**
 * db_connect.php  –  Database Configuration File
 * -----------------------------------------------
 * PURPOSE:
 *   This file is the single place where database credentials
 *   are defined. Every page that needs to query the database
 *   simply does:  include "db_connect.php";
 *   and then uses the $conn variable.
 *
 * BENEFIT:
 *   If you ever change your database host, username, or
 *   password, you only edit this ONE file — not every page.
 *
 * HOW TO SET UP:
 *   1. Open phpMyAdmin (http://localhost/phpmyadmin)
 *   2. Import DATABASE.sql  (this creates all tables + sample data)
 *   3. The credentials below match a default XAMPP/WAMP install.
 *      Change DB_PASS if your MySQL root has a password.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');               // change if your MySQL root has a password
define('DB_NAME', 'homeland_hospital');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("
        <div style='font-family:sans-serif;color:#c0392b;background:#fdf3f2;
                    border:1px solid #e8b4b1;border-radius:8px;
                    padding:32px;max-width:560px;margin:60px auto;'>
            <h2 style='margin-top:0'>&#9888; Database Connection Failed</h2>
            <p><strong>Error:</strong> " . htmlspecialchars(mysqli_connect_error()) . "</p>
            <p>Please make sure:</p>
            <ul>
                <li>XAMPP/WAMP MySQL service is <strong>running</strong></li>
                <li>You have imported <strong>DATABASE.sql</strong> via phpMyAdmin</li>
                <li>The database name is exactly: <strong>homeland_hospital</strong></li>
                <li>DB_USER / DB_PASS in <code>db_connect.php</code> are correct</li>
            </ul>
        </div>
    ");
}

// Use UTF-8 for all queries so accented characters store and display correctly
mysqli_set_charset($conn, 'utf8mb4');
?>
