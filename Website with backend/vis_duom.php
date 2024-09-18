<?php
// Include PhpSpreadsheet classes
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection settings
$servername = "localhost"; // Replace with your database host
$username = "user";
$password = "userview";
$dbname = "db2"; // Replace with your database name

// Create a new instance of Spreadsheet
$spreadsheet = new Spreadsheet();

// Create a new sheet
$sheet = $spreadsheet->getActiveSheet();

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from multiple tables
$sql = "SELECT 
    r.RINKINYS_PAVADINIMAS,
    r.RINKINYS_IKEL_DATA,
    o.ORGANIZACIJA_PAVADINIMAS,
    z.ZYME_PAVADINIMAS
FROM 
    rinkinys r
LEFT JOIN 
    organizacija o ON r.ORGANIZACIJA_ID = o.ORGANIZACIJA_ID
LEFT JOIN 
    buna b ON r.RINKINYS_ID = b.RINKINYS_ID
LEFT JOIN 
    zyme z ON b.ZYME_ID = z.ZYME_ID";

// Run the SQL query
$result = $conn->query($sql);

// Check if the query executed successfully
if ($result !== false) {
    // Check if there are rows in the result
    if ($result->num_rows > 0) {
        // Output column headers
        $sheet->setCellValue('A1', 'RINKINYS_PAVADINIMAS');
        $sheet->setCellValue('B1', 'RINKINYS_IKEL_DATA');
        $sheet->setCellValue('C1', 'ORGANIZACIJA_PAVADINIMAS');
        $sheet->setCellValue('D1', 'ZYME_PAVADINIMAS');
        
        // Output data into different columns
        $rowIndex = 2; // Start from row 2 (after headers)
        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowIndex, $row['RINKINYS_PAVADINIMAS']);
            $sheet->setCellValue('B' . $rowIndex, $row['RINKINYS_IKEL_DATA']);
            $sheet->setCellValue('C' . $rowIndex, $row['ORGANIZACIJA_PAVADINIMAS']);
            $sheet->setCellValue('D' . $rowIndex, $row['ZYME_PAVADINIMAS']);
            $rowIndex++;
        }
    } else {
        echo "No rows found in the result set";
    }
} else {
    echo "Error executing query: " . $conn->error;
}

// Close database connection
$conn->close();

// Create a new Xlsx Writer
$writer = new Xlsx($spreadsheet);

// Save the spreadsheet to a file
$writer->save('output.xlsx');

echo "Data has been exported to output.xlsx";
?>
