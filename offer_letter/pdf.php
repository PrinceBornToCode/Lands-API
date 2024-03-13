<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: * ");
header("Access-Control-Allow-Headers: *");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mzuniadmin";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$row_id = $_GET['row_id'];
$deed_number = $_GET['deed_number'];
$title_deed = $_GET['title_deed'];
$date = $_GET['date'];

// TCPDF library initialization
require_once('../TCPDF-main/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Min Of Lands');
$pdf->SetAuthor('Land Registry');
$pdf->SetTitle('Land Offer Letter');
$pdf->SetSubject('Land Offer');
$pdf->SetKeywords('Land, Offer, Letter');

// Set default header data
$pdf->SetHeaderData('logo-ct.png', PDF_HEADER_LOGO_WIDTH, 'Min Of Lands', "Mzuzu Land Registry");

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '',   12);

// Add an image to the PDF
$pdf->Image('logo-ct.png',   10,   10,   30,   30, 'JPG', 'http://www.lands.org', '', true,   150, '', false, false,   1, false, false, false);

// Add a title deed text
$pdf->writeHTMLCell(0,   0, '', '', 'Title Deed',   0,   1,   0, true, 'C', true);

// Add the deed number
$pdf->writeHTMLCell(0,   0, '', '', 'Deed Number: ' . $deed_number,   0,   1,   0, true, 'C', true);

// Add the title deed
$pdf->writeHTMLCell(0,   0, '', '', 'Title Deed: ' . $title_deed,   0,   1,   0, true, 'C', true);

// Add the date
$pdf->writeHTMLCell(0,   0, '', '', 'Date: ' . $date,   0,   1,   0, true, 'C', true);

// Add some additional statements
$pdf->writeHTMLCell(0,   0, '', '', 'This is a formal statement concerning the land title deed...',   0,   1,   0, true, 'C', true);

// Add a watermark
$pdf->startPageGroup();
$pdf->Image('logo-ct.png', '', '',  200,  200, '', '', '', false,  300, '', false, false,  0, false, false, false, 'T');

// Output the PDF document
$pdf->Output('land_title_deed.pdf', 'I');

// End of file
?>
