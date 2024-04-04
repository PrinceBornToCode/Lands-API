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

require_once(dirname(__FILE__) . '/../TCPDF-main/examples/barcodes/tcpdf_barcodes_1d_include.php');

require_once(dirname(__FILE__) . '/../TCPDF-main/examples/barcodes/tcpdf_barcodes_2d_include.php');
// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



// set the barcode content and type
$barcodeobj = new TCPDF2DBarcode('http://www.tcpdf.org', 'PDF417');
$barcodeobjdata = $barcodeobj->getBarcodeHTML(4, 4, 'black');

// Set document information
$pdf->SetCreator('Min Of Lands');
$pdf->SetAuthor('Land Registry');
$pdf->SetTitle('Land Offer Letter');
$pdf->SetSubject('Land Offer');
$pdf->SetKeywords('Land, Offer, Letter');



// Set default header data
$pdf->SetHeaderData('logo-ct.png', PDF_HEADER_LOGO_WIDTH, 'Min Of Lands', "Mzuzu Land Registry");

// Set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);



// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$today_date = date("Y-m-d H:i:s");
//Add a page
$pdf->AddPage();
$pdf->setFillColor(255, 255, 215);
// Set font
$pdf->SetFont('helvetica', '',   12);

// Add an image to the PDF
$pdf->Image('logo-ct.png',   10,   10,   30,   30, 'JPG', 'http://www.lands.org', '', true,   150, '', false, false,   1, false, false, false);



// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------



// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);


// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)


$pdf->Ln(10);

// set font
$pdf->setFont('times', 'BI', 12);


// set some text to print
$txt2 = <<<EOD
                            Title Deed 

        I Minister of Lands , Through Power given to me through constitution 
 establised on 13/09/2024 by the Act of Pariament to administer and manage all lands in Malawi
is now transfer Rights to own the land below to the deed title holder
           

EOD;

// print a block of text using Write()
$pdf->Write(0, $txt2, '', 0, 'C', true, 0, false, false, 0);


// set font
$pdf->setFont('freeserif', '', 12);
$txt3 = "\n Deed Title Holder :" . $title_deed;
$txt4 = "\n Deed Number :" . $deed_number;
$txt4 = "\n Date :" . $today_date;
// print a block of text using Write()
$pdf->Write(0, $txt3, '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, $txt4, '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0, 'Period : 99 years', '', 0, 'C', true, 0, false, false, 0);
$txt = "Blockchain Security Hash:\n \n" . $date;

$pdf->MultiCell(55, 5, $txt, 1, 'J', 1, 2, 125, 210, true);

// move pointer to last page





// Add a watermark
$pdf->startPageGroup();
$pdf->Image('logo-ct.png', '', '',  200,  200, '', '', '', false,  300, '', false, false,  0, false, false, false, 'T');

// Output the PDF document
$pdf->Output('land_title_deed.pdf', 'I');

// End of file
