<?php
// Database connection parameters
$servername = "localhost"; // Replace with your database host
$username = "user";
$password = "userview";
$dbname = "helpdesk"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract the "aprasymas" field value from the form
    $aprasymas = $_POST["aprasymas"];

    // Prepare an SQL statement to insert data into the database
    $sql = "INSERT INTO pagalba (pagalba_busena, pagalba_tesktas, pagalba_prad_data, pagalba_pab_data) VALUES ('neivykdytas', '$aprasymas', NOW(), NULL)";

    // Execute the SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>