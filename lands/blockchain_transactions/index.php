<?php

// Import your db_connection.php file
require_once("./../../config.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle errors
function handleError($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

// POST request handler for creating a new blockchain transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id']) || !isset($data['land_id']) || !isset($data['transaction_hash']) || !isset($data['payment_id'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO blockchain_transactions (user_id, land_id, transaction_hash, payment_id)
                                VALUES (:user_id, :land_id, :transaction_hash, :payment_id)");
        
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':land_id' => $data['land_id'],
            ':transaction_hash' => $data['transaction_hash'],
            ':payment_id' => $data['payment_id']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Blockchain transaction inserted successfully']);
        } else {
            handleError("Failed to insert blockchain transaction");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all blockchain transactions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM blockchain_transactions ORDER BY user_id");
        
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($transactions);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single blockchain transaction by user_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    try {
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("SELECT * FROM blockchain_transactions WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction) {
            http_response_code(200);
            echo json_encode($transaction);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Blockchain transaction not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a blockchain transaction
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['land_id']) || !isset($data['transaction_hash']) || !isset($data['payment_id'])) {
        handleError("Missing required fields");
    }

    try {
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("UPDATE blockchain_transactions SET land_id = :land_id, transaction_hash = :transaction_hash, payment_id = :payment_id WHERE user_id = :user_id");
        
        $stmt->execute([
            ':land_id' => $data['land_id'],
            ':transaction_hash' => $data['transaction_hash'],
            ':payment_id' => $data['payment_id'],
            ':user_id' => $userId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Blockchain transaction updated successfully']);
        } else {
            handleError("Failed to update blockchain transaction");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a blockchain transaction
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['user_id'])) {
    try {
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("DELETE FROM blockchain_transactions WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Blockchain transaction deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Blockchain transaction not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>