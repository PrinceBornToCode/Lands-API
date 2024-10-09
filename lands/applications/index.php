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

// POST request handler for creating a new application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['land_id']) || !isset($data['user_id']) || !isset($data['application_date']) ||
        !isset($data['state']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO applications (land_id, user_id, application_date, state, description)
                                VALUES (:land_id, :user_id, :application_date, :state, :description)");
        
        $stmt->execute([
            ':land_id' => $data['land_id'],
            ':user_id' => $data['user_id'],
            ':application_date' => $data['application_date'],
            ':state' => $data['state'],
            ':description' => $data['description']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Application inserted successfully']);
        } else {
            handleError("Failed to insert application");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all applications
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM applications ORDER BY application_date DESC");
        
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($applications);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single application by land_id and user_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['land_id']) && isset($_GET['user_id'])) {
    try {
        $landId = $_GET['land_id'];
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("SELECT * FROM applications WHERE land_id = :land_id AND user_id = :user_id");
        $stmt->execute([':land_id' => $landId, ':user_id' => $userId]);
        
        $application = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($application) {
            http_response_code(200);
            echo json_encode($application);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Application not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating an application
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['land_id']) && isset($_GET['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['state']) || !isset($data['description'])) {
        handleError("Missing required fields");
    }

    try {
        $landId = $_GET['land_id'];
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("UPDATE applications SET state = :state, description = :description WHERE land_id = :land_id AND user_id = :user_id");
        
        $stmt->execute([
            ':state' => $data['state'],
            ':description' => $data['description'],
            ':land_id' => $landId,
            ':user_id' => $userId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Application updated successfully']);
        } else {
            handleError("Failed to update application");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting an application
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['land_id']) && isset($_GET['user_id'])) {
    try {
        $landId = $_GET['land_id'];
        $userId = $_GET['user_id'];
        $stmt = $pdo->prepare("DELETE FROM applications WHERE land_id = :land_id AND user_id = :user_id");
        $stmt->execute([':land_id' => $landId, ':user_id' => $userId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Application deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Application not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>