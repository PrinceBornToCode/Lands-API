<?php


header("Access-Control-Allow-Origin: *");

header('Content-Type: *');

header('Access-Control-Allow-Heagers: *');
 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: * ");



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mzuzuadmin";



try {
 $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
 die("Database connection failed: " . $e->getMessage());
}