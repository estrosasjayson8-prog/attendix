<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "attendix_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>