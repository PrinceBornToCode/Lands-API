<?php

// Import your config.php file for database and Web3 connection
require_once("./../../config.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// POST request handler for creating a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if required fields are present
    if (!isset($data['full_name']) || !isset($data['date_of_birth']) || !isset($data['password_hash']) || 
        !isset($data['phone_number']) || !isset($data['nation_id_number']) || 
        !isset($data['role']) || !isset($data['gender']) || !isset($data['street']) || 
        !isset($data['city']) || !isset($data['postal_code']) || !isset($data['country'])) {
        handleError("Missing required fields");
    }

    try {
        // Generate new Ethereum account (private and public key)
        $newAccount = createNewAccount($eth); // Assume this function is in config.php
        
        // You should generate the private key separately, securely store it
        // Placeholder logic for keys
        $privateKey = "Private_Key_For_$newAccount";
        $publicKey = $newAccount;

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO users (full_name, date_of_birth, password_hash, phone_number, private_key, public_key, nation_id_number, role, gender, street, city, postal_code, country) 
                                VALUES (:full_name, :date_of_birth, :password_hash, :phone_number, :private_key, :public_key, :nation_id_number, :role, :gender, :street, :city, :postal_code, :country)");

        // Bind and execute the query with generated values
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':date_of_birth' => $data['date_of_birth'],
            ':password_hash' => $data['password_hash'],
            ':phone_number' => $data['phone_number'],
            ':private_key' => $privateKey,
            ':public_key' => $publicKey,
            ':nation_id_number' => $data['nation_id_number'],
            ':role' => $data['role'],
            ':gender' => $data['gender'],
            ':street' => $data['street'],
            ':city' => $data['city'],
            ':postal_code' => $data['postal_code'],
            ':country' => $data['country']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'User inserted successfully', 'account' => $newAccount, 'public_key' => $publicKey, 'private_key' => $privateKey]);
        } else {
            handleError("Failed to insert user");
        }

    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>
