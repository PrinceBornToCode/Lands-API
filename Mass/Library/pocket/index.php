<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");

require '../../common/conn.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

    // Read operation (fetch books)
    $data = json_decode(file_get_contents('php://input'), true);
    $pocket_id = (string) $_GET['pocket_id'];

  
    $stmt = $pdo->prepare('SELECT * FROM pockets WHERE pocket_id = :pocket_id');
    $stmt->bindParam(':pocket_id', $pocket_id);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode($result); 




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
            // Decode the JSON body
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);

            $pocket_id = $data['pockets_id']?? null;
            $charge = $data['charge']?? null;
            $receipt_number = $data['receipt_number']?? null;

            if ($pocket_id === null || $charge === null || $receipt_number === null) {
                // Bad Request
                echo json_encode(['message' => 'Missing required parameters']);
                exit();
            }

            // Start a transaction
            $pdo->beginTransaction();

            try {
                // Fetch user_id and book_id from pockets
                $stmt = $pdo->prepare("SELECT * FROM pockets WHERE pocket_id =?");
                $stmt->execute([$pocket_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $is_cleared = "1";


                if (!$result) {
                    throw new Exception('Pocket ID not found');
                }

                $user_id = $result['issued_by'];
                $book_id = $result['book_id'];

                // Update book_details and user_details
                $stmt = $pdo->prepare('UPDATE book_details SET is_borrowed=0 WHERE book_id=?');
                $stmt->execute([$book_id]);

                $stmt = $pdo->prepare('UPDATE user_details SET borrowed_books=0 WHERE user_id=?');
                $stmt->execute([$user_id]);

                

                // Update pockets
                $stmt = $pdo->prepare('UPDATE pockets SET charge=?, is_creared=? WHERE pocket_id=?');
                $stmt->execute([$charge, $is_cleared, $pocket_id]);



               
                // Commit the transaction
                $pdo->commit();

                // Echo a JSON response indicating success
                echo json_encode(['message' => 'User updated successfully']);
            } catch (Exception $e) {
                // Rollback the transaction in case of error
                $pdo->rollBack();
                echo json_encode(['message' => 'Error updating user', 'error' => $e->getMessage()]);
            }

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
