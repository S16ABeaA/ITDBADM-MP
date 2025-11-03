<?php
define('DB_SERVER', 'localhost');   // db server
define('DB_USERNAME', 'root');      // username
define('DB_PASSWORD', 'Dlsu1234!'); // password
define('DB_NAME', 'itdbadm');       // database name

try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    } else {
        // Connection successful
        // Only for debugging purposes
        echo "<script>alert('Connected successfully');</script>";
    }
} catch (Exception $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>