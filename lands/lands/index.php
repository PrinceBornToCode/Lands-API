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

// POST request handler for creating a new land
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['type']) || !isset($data['coordinates']) || !isset($data['state']) ||
        !isset($data['owner_id']) || !isset($data['landlord']) || !isset($data['size']) || !isset($data['price'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO land (type, coordinates, state, owner_id, landlord, size, price)
                                VALUES (:type, :coordinates, :state, :owner_id, :landlord, :size, :price)");
        
        $stmt->execute([
            ':type' => $data['type'],
            ':coordinates' => $data['coordinates'],
            ':state' => $data['state'],
            ':owner_id' => $data['owner_id'],
            ':landlord' => $data['landlord'],
            ':size' => $data['size'],
            ':price' => $data['price']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Land inserted successfully']);
        } else {
            handleError("Failed to insert land");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all lands
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM land ORDER BY type");
        
        $lands = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($lands);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single land by owner_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['owner_id'])) {
    try {
        $ownerId = $_GET['owner_id'];
        $stmt = $pdo->prepare("SELECT * FROM land WHERE owner_id = :owner_id");
        $stmt->execute([':owner_id' => $ownerId]);
        
        $land = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($land) {
            http_response_code(200);
            echo json_encode($land);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Land not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a land
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['owner_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['type']) || !isset($data['coordinates']) || !isset($data['state']) ||
        !isset($data['landlord']) || !isset($data['size']) || !isset($data['price'])) {
        handleError("Missing required fields");
    }

    try {
        $ownerId = $_GET['owner_id'];
        $stmt = $pdo->prepare("UPDATE land SET type = :type, coordinates = :coordinates, state = :state, landlord = :landlord, size = :size, price = :price WHERE owner_id = :owner_id");
        
        $stmt->execute([
            ':type' => $data['type'],
            ':coordinates' => $data['coordinates'],
            ':state' => $data['state'],
            ':landlord' => $data['landlord'],
            ':size' => $data['size'],
            ':price' => $data['price'],
            ':owner_id' => $ownerId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Land updated successfully']);
        } else {
            handleError("Failed to update land");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a land
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['owner_id'])) {
    try {
        $ownerId = $_GET['owner_id'];
        $stmt = $pdo->prepare("DELETE FROM land WHERE owner_id = :owner_id");
        $stmt->execute([':owner_id' => $ownerId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Land deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Land not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>