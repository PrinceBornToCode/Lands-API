<?php


$servername = "localhost";
$username = "u953672825_user";
$password = "1347Happy";
$dbname = "u953672825_mzuniadmin";



try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
