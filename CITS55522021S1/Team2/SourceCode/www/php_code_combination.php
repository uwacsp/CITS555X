<?php
	require('../private_html/db.php');
?>


<?php
	session_start();

	// initialize variables
	$deviceID = "";
	$attributeId = "";
	$id = 0;
	$update = false;

	if (isset($_POST['save'])) {
		$deviceID = $_POST['deviceID'];
		$attributeId = $_POST['attributeId'];
 
		$sql = "INSERT INTO ems_combination_table (device_id, attribute_id) VALUES ('$deviceID', '$attributeId')"; 
		$result = $db->query($sql);
		
		
		$_SESSION['message'] = "New Combination saved"; 
		header('location: combination_index.php');
	}
	
	if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$deviceID = $_POST['deviceID'];
	$attributeId = $_POST['attributeId'];

	$sql = "UPDATE ems_combination_table SET device_id='$deviceID', attribute_id='$attributeId' WHERE comb_id=$id"; 
	$result = $db->query($sql);
	
	$_SESSION['message'] = "Combination updated!"; 
	header('location: combination_index.php');
}

	if (isset($_GET['del'])) {
	$id = $_GET['del'];
	
	$sql = "DELETE FROM ems_combination_table WHERE comb_id=$id"; //changed this
	$result = $db->query($sql);
	$_SESSION['message'] = "Combination deleted!"; 
	header('location: combination_index.php');
}
?>