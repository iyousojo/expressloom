<?php
// Connect to the user database
$conn = new mysqli('localhost', 'root', '', 'solomon');
if ($conn->connect_error) {
    die("Connection failed to User DB: " . $conn->connect_error);
}

// Connect to the CDM logistics database
$logistics_db = new mysqli('localhost', 'root', '', 'cms_db');
if ($logistics_db->connect_error) {
    die("Connection failed to Logistics DB: " . $logistics_db->connect_error);
}
?>