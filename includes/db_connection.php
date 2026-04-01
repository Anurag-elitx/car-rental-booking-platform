<?php
/**
 * Database connection using PDO.
 * Adjust your DB credentials as needed.
 */

/* 
 * DATABASE CONFIGURATION
 * Smart switching between Local (XAMPP) and Production (InfinityFree)
 */

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    // LOCAL XAMPP/WAMP
    $host = 'localhost';
    $db   = 'car_rental_db';
    $user = 'root';
    $pass = 'another'; 
} else {
    // PRODUCTION (InfinityFree)
    $host = 'sql308.infinityfree.com';
    $db   = 'if0_41562124_obsidian';
    $user = 'if0_41562124';
    $pass = 'aPx6rfbHCI'; 
}
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     if ($e->getCode() == 1045) {
          die("<div style='font-family:sans-serif; text-align:center; padding: 50px; background:#111; color:#fff; height:100vh;'>
                <h1 style='color:#ff4444;'>Database Access Denied!</h1>
                <p>It looks like your MySQL server requires a password for the 'root' user.</p>
                <p>Please open <b>includes/db_connection.php</b> and enter your password on line 10:</p>
                <code style='background:#222; padding:10px; display:inline-block; color:#0f0;'>\$pass = 'your_password_here';</code>
              </div>");
     }
     die("Database connection failed: " . $e->getMessage());
}
?>