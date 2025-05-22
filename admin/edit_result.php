<?php
include 'db_connect.php';
$qry = $logistics_db->query("SELECT r.*,concat(s.firstname,' ',s.middlename,' ',s.lastname) as name,s.student_code,concat(c.level,'-',c.section) as class FROM results r INNER JOIN classes c ON c.id = r.class_id INNER JOIN students s ON s.id = r.student_id WHERE r.id = " . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
    $$k = $v;
}
include 'new_result.php';
?>