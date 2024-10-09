<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../common/conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT count(book_id) as totalBooks FROM book_details');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        $user_id = $data['userId'];
        $first_name = $data['first_name'];
        $password = "Mass";
        $surname = $data['surname'];
        $role = $data['role'];
        $class = $data['class'];
        $stream = $data['stream'];
       
 
       
        $stmt = $pdo->prepare('INSERT INTO user_details(user_id, first_name, surname, role, password, class, stream) VALUES(?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $first_name, $surname, $role, $password, $class, $stream]);

        echo json_encode(['message' => 'User added successfully']);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $land = $data['land'];
        $land_process = $data['land_process'];
        $private_key = $data['private_key'];
        $public_key = $data['public_key'];
        $wallet_address = $data['wallet_address'];

        $stmt = $pdo->prepare('UPDATE users SET land=?, land_process=?, private_key=?, public_key=?, wallet_address=? WHERE id=?');
        $stmt->execute([$land, $land_process, $private_key, $public_key, $wallet_address, $id]);

        echo json_encode(['message' => 'User updated successfully']);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['message' => 'User deleted successfully']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
