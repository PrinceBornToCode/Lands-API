<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../common/conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
    $data = json_decode(file_get_contents('php://input'), true);
  

$searchTerm = $_GET['book_id']; // Get the search term from the GET request

$query = "SELECT * FROM book_details  WHERE book_id LIKE?";
$stmt = $pdo->prepare($query);
$stmt->bind_param('s', '%'. $searchTerm. '%'); // Bind the search term to the query
$stmt->execute();
$result = $stmt->get_result();

$suggestions = array();
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row['book_id']; 
}

echo json_encode($suggestions); // Return the suggestions as JSON


        break;


    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $author = $data['author'];
        $date_of_published = $data['date_of_published'];
        $book_description = $data['book_description'];
        $date_entered = $data['date_entered'];
        $date_entered = $data['date_entered'];
         $is_borrowed = $data['is_borrowed'];
       
 
       




        $stmt = $pdo->prepare('INSERT INTO book_details( title, author, date_of_published, book_description, date_entered, is_borrowed) VALUES( ?, ?, ?, ?, ?, ?)');
        $stmt->execute([ $title, $author, $date_of_published, 
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
