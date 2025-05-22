<?php
include 'db_connect.php';
$qry = $logistics_db->query("SELECT * FROM branches WHERE id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
    $$k = $v;
}
include 'new_branch.php';
?>