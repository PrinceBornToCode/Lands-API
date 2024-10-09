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

// POST request handler for creating a new offer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['application_id']) || !isset($data['offer_date']) || !isset($data['state']) ||
        !isset($data['developmental_charge']) || !isset($data['transaction_id'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO offers (application_id, offer_date, state, developmental_charge, transaction_id)
                                VALUES (:application_id, :offer_date, :state, :developmental_charge, :transaction_id)");
        
        $stmt->execute([
            ':application_id' => $data['application_id'],
            ':offer_date' => $data['offer_date'],
            ':state' => $data['state'],
            ':developmental_charge' => $data['developmental_charge'],
            ':transaction_id' => $data['transaction_id']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Offer inserted successfully']);
        } else {
            handleError("Failed to insert offer");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all offers
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM offers ORDER BY offer_date DESC");
        
        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($offers);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single offer by transaction_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['transaction_id'])) {
    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("SELECT * FROM offers WHERE transaction_id = :transaction_id");
        $stmt->execute([':transaction_id' => $transactionId]);
        
        $offer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($offer) {
            http_response_code(200);
            echo json_encode($offer);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Offer not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating an offer
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['transaction_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['application_id']) || !isset($data['offer_date']) || !isset($data['state']) ||
        !isset($data['developmental_charge'])) {
        handleError("Missing required fields");
    }

    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("UPDATE offers SET application_id = :application_id, offer_date = :offer_date, state = :state, developmental_charge = :developmental_charge WHERE transaction_id = :transaction_id");
        
        $stmt->execute([
            ':application_id' => $data['application_id'],
            ':offer_date' => $data['offer_date'],
            ':state' => $data['state'],
            ':developmental_charge' => $data['developmental_charge'],
            ':transaction_id' => $transactionId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Offer updated successfully']);
        } else {
            handleError("Failed to update offer");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting an offer
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['transaction_id'])) {
    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("DELETE FROM offers WHERE transaction_id = :transaction_id");
        $stmt->execute([':transaction_id' => $transactionId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Offer deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Offer not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>