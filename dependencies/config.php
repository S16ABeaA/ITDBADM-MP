<?php
define('DB_SERVER', '127.0.0.1');     
define('DB_USERNAME', 'student1');  
define('DB_PASSWORD', 'Dlsu1234!');   
define('DB_NAME', 'AnimoBowl');       
define('DB_PORT', 3307);         

try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    } else {
        echo "Connected successfully";
    }
} catch (Exception $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>

