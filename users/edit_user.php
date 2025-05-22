<?php
include 'link.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $qry = $conn->query("SELECT *, concat(last_name, ', ', first_name, ' ', middle_name) as name FROM users WHERE id = $id")->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
    include 'new_user.php';
} else {
    echo "Invalid user ID.";
}
?>