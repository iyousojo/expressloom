<?php 

// Connect to the logistics database (cms_db)
$logistics_db = new mysqli('localhost', 'root', '', 'cms_db');
if ($logistics_db->connect_error) {
    die("Connection failed to Logistics DB: " . $logistics_db->connect_error);
}

// Connect to the user database (solomon)
$user_db = new mysqli('localhost', 'root', '', 'solomon');
if ($user_db->connect_error) {
    die("Connection failed to User DB: " . $user_db->connect_error);
}
?>