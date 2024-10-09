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

// POST request handler for creating a new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['service_provider']) || !isset($data['amount']) || !isset($data['transaction_id']) ||
        !isset($data['deed_number']) || !isset($data['type']) || !isset($data['payment_date'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO payments (service_provider, amount, transaction_id, deed_number, type, payment_date)
                                VALUES (:service_provider, :amount, :transaction_id, :deed_number, :type, :payment_date)");
        
        $stmt->execute([
            ':service_provider' => $data['service_provider'],
            ':amount' => $data['amount'],
            ':transaction_id' => $data['transaction_id'],
            ':deed_number' => $data['deed_number'],
            ':type' => $data['type'],
            ':payment_date' => $data['payment_date']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Payment inserted successfully']);
        } else {
            handleError("Failed to insert payment");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all payments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM payments ORDER BY payment_date DESC");
        
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($payments);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single payment by transaction_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['transaction_id'])) {
    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_id = :transaction_id");
        $stmt->execute([':transaction_id' => $transactionId]);
        
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($payment) {
            http_response_code(200);
            echo json_encode($payment);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Payment not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a payment
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['transaction_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['service_provider']) || !isset($data['amount']) || !isset($data['deed_number']) ||
        !isset($data['type']) || !isset($data['payment_date'])) {
        handleError("Missing required fields");
    }

    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("UPDATE payments SET service_provider = :service_provider, amount = :amount, deed_number = :deed_number, type = :type, payment_date = :payment_date WHERE transaction_id = :transaction_id");
        
        $stmt->execute([
            ':service_provider' => $data['service_provider'],
            ':amount' => $data['amount'],
            ':deed_number' => $data['deed_number'],
            ':type' => $data['type'],
            ':payment_date' => $data['payment_date'],
            ':transaction_id' => $transactionId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Payment updated successfully']);
        } else {
            handleError("Failed to update payment");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a payment
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['transaction_id'])) {
    try {
        $transactionId = $_GET['transaction_id'];
        $stmt = $pdo->prepare("DELETE FROM payments WHERE transaction_id = :transaction_id");
        $stmt->execute([':transaction_id' => $transactionId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Payment deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Payment not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>