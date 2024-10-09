<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../common/conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
 
    $stmt = $pdo->query('SELECT * FROM pockets WHERE is_creared = 0');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
       

    echo json_encode($result);

       
        break;
  
case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'];
    $book_id = $data['book_id'];
    $is_borrowed = 1;
    $pocket_id = $user_id. "/". $book_id. "/". date('Ymd');

    $now = new DateTime();
    $now->modify('3 days'); // Move the modification inside the case statement

//     $now = new DateTime();
// $now->add(new DateInterval('PT3M')); // Adds 3 minutes to the current time


    $is_cleared = 0;
    $charge = 0;

    $expected_return_date = $now->format('Y-m-d');
    $borrowed_date = $now->format('Y-m-d'); // Format borrowed_date as a string

    $stmt = $pdo->prepare('UPDATE book_details SET is_borrowed=? WHERE book_id=?');
    $stmt->execute([$is_borrowed, $book_id]);

    $stmt = $pdo->prepare('UPDATE user_details SET borrowed_books=borrowed_books+? WHERE user_id=?');
    $stmt->execute([1, $user_id]);

    $stmt = $pdo->prepare('INSERT INTO pockets(
        pocket_id, book_id, issued_by,
        borrowed_date, expected_return_date,is_creared, charge) 
    VALUES(?,?,?,?,?,?,?)');
    $stmt->execute([
        $pocket_id, $book_id, $user_id, 
        $borrowed_date, $expected_return_date, $is_cleared, $charge]);

    echo json_encode(['message' => 'User added successfully']);
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
