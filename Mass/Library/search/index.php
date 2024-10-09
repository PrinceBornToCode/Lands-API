<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../common/conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
  
     
        try {
        $stmt = $pdo->query('SELECT * FROM book_details WHERE is_borrowed = 0');
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
             $stmt1 = $pdo->query('SELECT * FROM user_details');
        $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
   echo json_encode($result);
  
        } catch (Exception $e) {
              echo json_encode($e);
              break;
        }
           echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $book_id = $data['book_id'];
        $title = $data['title'];
        $author = $data['author'];
        $date_of_published = $data['date_of_published'];
        $book_description = $data['book_description'];
        $date_entered = $data['date_entered'];
        $date_entered = $data['date_entered'];
         $is_borrowed = $data['is_borrowed'];
       
 

        $stmt = $pdo->prepare('INSERT INTO book_details( book_id, title, author, date_of_published, book_description, date_entered, is_borrowed) VALUES(?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$book_id, $title, $author, $date_of_published, 
            $book_description, $date_entered, $is_borrowed]);

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
