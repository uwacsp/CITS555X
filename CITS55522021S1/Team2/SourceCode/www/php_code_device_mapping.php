<?php
	require('../private_html/db.php');
?>


<?php
	session_start();

	// initialize variables
	$deviceName = "";
	$deviceId = "";
	$id = 0;
	$update = false;

	if (isset($_POST['save'])) {
		$deviceName = $_POST['deviceName'];
		$deviceId = $_POST['deviceId'];
 
		$sql = "INSERT INTO ems_device_mapping_table (device_name_ext, device_id) VALUES ('$deviceName','$deviceId')"; 
		$result = $db->query($sql);
		
		
		$_SESSION['message'] = "Device mapping saved"; 
		header('location: device_mapping_index.php');
	}
	
	if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$deviceName = $_POST['deviceName'];
	$deviceId = $_POST['deviceId'];

	$sql = "UPDATE ems_device_mapping_table SET device_name_ext='$deviceName', device_id='$deviceId' WHERE device_id=$id"; 
	$result = $db->query($sql);
	
	$_SESSION['message'] = "Device mapping updated!"; 
	header('location: device_mapping_index.php');
}

	if (isset($_GET['del'])) {
	$id = $_GET['del'];
	
	$sql = "DELETE FROM ems_device_mapping_table WHERE device_id=$id"; 
	$result = $db->query($sql);
	$_SESSION['message'] = "Device mapping deleted!"; 
	header('location: device_mapping_index.php');
}
?>