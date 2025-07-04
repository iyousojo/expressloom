<?php 
include 'db_connect.php';
$ids = $_GET['ids'];
$parcels = $logistics_db->query("SELECT * FROM parcels WHERE id IN ($ids)");
$branch = array();
$branches = $logistics_db->query("SELECT *,concat(street,', ',city,', ',state,', ',zip_code,', ',country) as address FROM branches WHERE id IN (SELECT from_branch_id FROM parcels WHERE id IN ($ids)) OR id IN (SELECT to_branch_id FROM parcels WHERE id IN ($ids))");
while ($row = $branches->fetch_assoc()):
    $branch[$row['id']] = $row['address'];
endwhile;

while ($row = $parcels->fetch_assoc()):
?>
<table width="100%">
	<tr>
		<td colspan="3">Tracking Number : <b><?php echo $row['reference_number'] ?></b></td>
	</tr>
	<tr>
		<td width="33.33%">
			<p><b>Sender: <?php echo ucwords($row['sender_name']); ?></b></p>
		</td>
		<td width="33.33%">
			<p><b>Recipient: <?php echo ucwords($row['recipient_name']); ?></b></p>
		</td>
		<td width="33.33%"></td>
	</tr>
</table>
<?php endwhile; ?>

