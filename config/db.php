<?php
$host = "localhost";
$user = "root";
$pass = "";        // XAMPP mein by default password empty hota hai
$db   = "blood_db";
 
$conn = new mysqli($host, $user, $pass, $db);
 
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "error"   => "DB connect failed: " . $conn->connect_error
    ]));
}
 
$conn->set_charset("utf8");
?>
 