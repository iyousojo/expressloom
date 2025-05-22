<?php
$conn = new mysqli('localhost', 'root', '', 'solomon');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>