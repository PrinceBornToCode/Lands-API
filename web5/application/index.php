<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require_once("../../conn.php");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read operation (fetch applications with user details)
        $stmt = $pdo->query("
            SELECT land.*, users.*
            FROM land
            INNER JOIN users ON land.owner_id = users.id WHERE land.approved = 3
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;


    case 'POST':
        // Create operation (add a new application)
        $data = json_decode(file_get_contents('php://input'), true);

        $user_id = $data['from'];
        $land_id = $data['land_id'];
        $to_id = $data['to_id'];
        $nature = $data['nature'];
        $description = $data['description'];
        $national_id = $data['national_id'];

        $application_date = date("Y-m-d H:i:s");
        $accepted = "0";

        $stmt = $pdo->prepare("INSERT INTO applications (user_id, land_id, application_date, accepted, to_id, nature, description, national_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $land_id, $application_date, $accepted, $to_id, $nature, $description, $national_id]);

        echo json_encode(['message' => 'Application added successfully']);
        break;



    case 'PUT':
        // Update operation (edit an application)
        parse_str(file_get_contents('php://input'), $data);
        $id = $data['id'];
        $user_id = $data['user_id'];
        $land_id = $data['land_id'];
        $application_date = $data['application_date'];
        $accepted = $data['accepted'];
        $to = $data['to'];
        $nature = $data['nature'];

        $stmt = $pdo->prepare('UPDATE applications SET user_id=?, land_id=?, application_date=?, accepted=?, to=?, nature=? WHERE id=?');
        $stmt->execute([$user_id, $land_id, $application_date, $accepted, $to, $nature, $id]);

        echo json_encode(['message' => 'Application updated successfully']);
        break;

    case 'DELETE':
        // Delete operation (remove an application)
        $id = $_GET['id'];

        $stmt = $pdo->prepare('DELETE FROM applications WHERE id=?');
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Application deleted successfully']);
        break;

    default:
        // Invalid method
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
