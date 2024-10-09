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

// POST request handler for creating a new title deed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['offer_id']) || !isset($data['state']) || !isset($data['title_deed']) ||
        !isset($data['deed_number']) || !isset($data['type']) || !isset($data['expire_date'])) {
        handleError("Missing required fields");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO title_deeds (offer_id, state, title_deed, deed_number, type, expire_date)
                                VALUES (:offer_id, :state, :title_deed, :deed_number, :type, :expire_date)");
        
        $stmt->execute([
            ':offer_id' => $data['offer_id'],
            ':state' => $data['state'],
            ':title_deed' => $data['title_deed'],
            ':deed_number' => $data['deed_number'],
            ':type' => $data['type'],
            ':expire_date' => $data['expire_date']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(['message' => 'Title deed inserted successfully']);
        } else {
            handleError("Failed to insert title deed");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching all title deeds
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM title_deeds ORDER BY offer_id");
        
        $titleDeeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($titleDeeds);
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// GET request handler for fetching a single title deed by offer_id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['offer_id'])) {
    try {
        $offerId = $_GET['offer_id'];
        $stmt = $pdo->prepare("SELECT * FROM title_deeds WHERE offer_id = :offer_id");
        $stmt->execute([':offer_id' => $offerId]);
        
        $titleDeed = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($titleDeed) {
            http_response_code(200);
            echo json_encode($titleDeed);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Title deed not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// PUT request handler for updating a title deed
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['offer_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['state']) || !isset($data['title_deed']) ||
        !isset($data['deed_number']) || !isset($data['type']) || !isset($data['expire_date'])) {
        handleError("Missing required fields");
    }

    try {
        $offerId = $_GET['offer_id'];
        $stmt = $pdo->prepare("UPDATE title_deeds SET state = :state, title_deed = :title_deed, deed_number = :deed_number, type = :type, expire_date = :expire_date WHERE offer_id = :offer_id");
        
        $stmt->execute([
            ':state' => $data['state'],
            ':title_deed' => $data['title_deed'],
            ':deed_number' => $data['deed_number'],
            ':type' => $data['type'],
            ':expire_date' => $data['expire_date'],
            ':offer_id' => $offerId
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Title deed updated successfully']);
        } else {
            handleError("Failed to update title deed");
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

// DELETE request handler for deleting a title deed
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['offer_id'])) {
    try {
        $offerId = $_GET['offer_id'];
        $stmt = $pdo->prepare("DELETE FROM title_deeds WHERE offer_id = :offer_id");
        $stmt->execute([':offer_id' => $offerId]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Title deed deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Title deed not found']);
        }
    } catch (PDOException $e) {
        handleError("Database error: " . $e->getMessage());
    }

    // Close the PDO connection
    $pdo = null;
}

?>