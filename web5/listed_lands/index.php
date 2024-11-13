<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require_once("../../conn.php");



$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        $approved = 2;
        $stmt = $pdo->prepare('SELECT land.*,users.*
        FROM land
        INNER JOIN users ON land.owner_id = users.id
        WHERE approved=:approved');
        $stmt->execute([':approved' => $approved]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        // Create operation (add a new land)
        $data = json_decode(file_get_contents('php://input'), true);


        $land_id = $_POST['land_id'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $size = $_POST['size'];
        $land_loard =   isset($_POST['land_loard']) ? $_POST['land_loard'] : 'lands';


        $owner_id = isset($_POST['owner_id']) ? $_POST['owner_id'] : '';
        $price = isset($_POST['land_loard']) ? "60000" : "0";
        $duration = "Not Registered";
        $approved = "0";


        $layout =   $_POST['selectedFile'];




        $stmt = $pdo->prepare('INSERT INTO land (
            land_id, owner_id, description,
             type, size, land_loard,
              layout, price, duration,
               approved
           ) VALUES (
           ?, ?, ?, 
           ?, ?, ?,
            ?, ?, ?,

             ?)');

        $stmt->execute([$land_id, $owner_id, $description, $type, $size, $land_loard, $layout, $price, $duration, $approved]);



        echo json_encode(['message' => 'Land added successfully']);
        break;


// Assuming this is part of a larger switch-case block

case 'PUT':
    // Fetch the JSON input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Make sure all required fields are present in the input
    if (!isset($input['land_code']) || !isset($input['price']) || !isset($input['owner'])) {
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Assign variables from the input
    $land_id = $input['land_code'];
    $price = $input['price'];
    $user_id = $input['owner'];
    $approved = 1;

    try {
        // Prepare the SQL statement to update the land entry
        $stmt = $pdo->prepare('UPDATE land SET price = ?, owner_id = ? , approved =? WHERE land_id = ?');
        $result = $stmt->execute([$price, $user_id, $land_id , $approved]);

        if ($result) {
            echo json_encode(['message' => 'Land updated successfully', 'updated_land_id' => $land_id]);
        } else {
            echo json_encode(['message' => 'No changes made, or land not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    break;



    case 'DELETE':
        // Delete operation (remove a land)
        $id = $_GET['id'];

        $stmt = $pdo->prepare('DELETE FROM land WHERE id=?');
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Land deleted successfully']);
        break;

    default:
        // Invalid method
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
