
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mzuzuadmin";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$data = json_decode(file_get_contents('php://input'), true);

$deed_number = isset($_POST['deed_number']) ? $_POST['deed_number'] : '';
$owner_phone_number = isset($_POST['owner_phone_number']) ? $_POST['owner_phone_number'] : '';

$full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
$approved = '0';

$expiary_date =  date("Y-m-d H:i:s");

if (isset($_FILES['selectedFile']['error']) && $_FILES['selectedFile']['error'] == 0) {
    $folderName = './'; // Specify the folder where you want to store the uploaded images

    $path = $_FILES['selectedFile']['name'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    $fileName = 'mz' . rand(10000, 10000000) . '_' . rand(100000, 100000000) . '_' . time();
    $fileName = strtolower(str_replace(' ', '_', $fileName));
    $fileName .= '.' . $ext;

    $destination = $folderName . '/' . $fileName;
    $source = $_FILES['selectedFile']['tmp_name'];

    move_uploaded_file($source, $destination);
    $landUrl = 'http://localhost/backend/images/' . $fileName;

    $stmt = $pdo->prepare('INSERT INTO `title_deeds`(`deed_number`, `owner_phone_number`, `approved`, `expiary_date`, `owner_full_name`) VALUES (?, ?, ?, ?, ?)');

    $stmt->execute([
        $deed_number, $owner_phone_number, $approved,
        $expiary_date`, $owner_full_name
    ]);

    echo json_encode(['message' => 'Data inserted successfully']);
} else {
    echo json_encode(['message' => 'Error uploading file']);
}
?>
