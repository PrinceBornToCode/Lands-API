<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM blockchain_transactions');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);


        $title_deed = $data['title_deed'];
        $type = $data['type'];
        $offer_number = $data['offer_number'];
        $deed_number = $data['deed_number'];


        $approved = isset($data['gr_no']) ? "1" : "0";
        $expiry_date = date("Y-m-d H:i:s");


        $owner_phone_number = $data['gr_no'];



        $stmt = $pdo->prepare('INSERT INTO title_deeds (offer_number, deed_number, owner_phone_number, approved, expiary_date, title_deed, type) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$offer_number, $deed_number, $owner_phone_number, $approved, $expiry_date, $title_deed, $type]);

        echo json_encode(['message' => 'Title deed added successfully']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        $deed_number = $data['deed_number'];
        $approved = isset($data['gr_no']) ? 1 : 0;  // Use a boolean value for 'approved'
        $title_deed = $data['title_deed'];


        $stmt = $pdo->prepare('UPDATE title_deeds SET approved = ?, title_deed = ? WHERE deed_number = ?');
        $stmt->execute([$approved, $title_deed, $deed_number]);

        echo json_encode(['message' => 'Title deed updated successfully']);
        break;

    case 'DELETE':
        $id = $_GET['id'];

        $stmt = $pdo->prepare('DELETE FROM title_deeds WHERE id=?');
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Title deed deleted successfully']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
