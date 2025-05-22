<?php
include 'db_connect.php';
$qry = $user_db->query("SELECT * FROM users WHERE id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
    $$k = $v;
}
// Do not pass the password to the included file
unset($password);
include 'new_user.php';
?>