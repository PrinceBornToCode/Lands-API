<?php


header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin: *");
header('Content-Type: *');
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "masslibrarysystem";

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "mzuniadmin";



try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
