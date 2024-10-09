<?php

// Import your db_connection.php file
require_once("./../../config.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Create a PDO instance

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// POST request handler for creating a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['full_name']) || !isset($data['date_of_birth']) || !isset($data['password_hash']) || 
        !isset($data['phone_number']) || !isset($data['private_key']) || !isset($data['public_key']) || 
        !isset($data['nation_id_number']) || !isset($data['role']) || !isset($data['gender']) || 
        !isset($data['street']) || !isset($data['city']) || !isset($data['postal_code']) || !isset($data['country'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, date_of_birth, password_hash, phone_number, private_key, public_key, nation_id_number, role, gender, street, city, postal_code, country) 
                                VALUES (:full_name, :date_of_birth, :password_hash, :phone_number, :private_key, :public_key, :nation_id_number, :role, :gender, :street, :city, :postal_code, :country)");
        
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':date_of_birth' => $data['date_of_birth'],
            ':password_hash' => $data['password_hash'],
            ':phone_number' => $data['phone_number'],
            ':private_key' => $data['private_key'],
            ':public_key' => $data['public_key'],
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
            echo json_encode(['message' => 'User inserted successfully']);
        } else {
            handleError("Failed to insert user");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all users
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY full_name");
        
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($users);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single user by phone_number
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['phone_number'])) {
    try {
        $phone_number = $_GET['phone_number'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone_number = :phone_number");
        $stmt->execute([':phone_number' => $phone_number]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a user
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['phone_number'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['full_name']) || !isset($data['date_of_birth']) || !isset($data['password_hash']) || 
        !isset($data['phone_number']) || !isset($data['private_key']) || !isset($data['public_key']) || 
        !isset($data['nation_id_number']) || !isset($data['role']) || !isset($data['gender']) || 
        !isset($data['street']) || !isset($data['city']) || !isset($data['postal_code']) || !isset($data['country'])) {
        handleError("Missing required fields");
    }

    try {
        $phone_number = $_GET['phone_number'];
        $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, date_of_birth = :date_of_birth, password_hash = :password_hash, phone_number = :phone_number, private_key = :private_key, public_key = :public_key, nation_id_number = :nation_id_number, role = :role, gender = :gender, street = :street, city = :city, postal_code = :postal_code, country = :country WHERE phone_number = :phone_number");
        
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':date_of_birth' => $data['date_of_birth'],
            ':password_hash' => $data['password_hash'],
            ':phone_number' => $phone_number,
            ':private_key' => $data['private_key'],
            ':public_key' => $data['public_key'],
            ':nation_id_number' => $data['nation_id_number'],
            ':role' => $data['role'],
            ':gender' => $data['gender'],
            ':street' => $data['street'],
            ':city' => $data['city'],
            ':postal_code' => $data['postal_code'],
            ':country' => $data['country']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            handleError("Failed to update user");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['phone_number'])) {
    try {
        $phone_number = $_GET['phone_number'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE phone_number = :phone_number");
        $stmt->execute([':phone_number' => $phone_number]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>