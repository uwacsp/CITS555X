<?php
	require('../private_html/db.php');
?>


<?php
	session_start();

	// initialize variables
	$deviceName = "";
	$systemName = "";
	$id = 0;
	$update = false;

	if (isset($_POST['save'])) {
		$deviceName = $_POST['deviceName'];
		$systemName = $_POST['systemName'];
 
		$sql = "INSERT INTO ems_device_table (device_name, pvsystem) VALUES ('$deviceName', '$systemName')"; 
		$result = $db->query($sql);
		
		
		$_SESSION['message'] = "Device saved"; 
		header('location: device_operation_index.php');
	}
	
	if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$deviceName = $_POST['deviceName'];
	$systemName = $_POST['systemName'];

	$sql = "UPDATE ems_device_table SET device_name='$deviceName', pvsystem='$systemName' WHERE device_id=$id"; 
	$result = $db->query($sql);
	
	$_SESSION['message'] = "Device updated!"; 
	header('location: device_operation_index.php');
}

	if (isset($_GET['del'])) {
	$id = $_GET['del'];
	
	$sql = "DELETE FROM ems_device_table WHERE device_id=$id"; 
	$result = $db->query($sql);
	$_SESSION['message'] = "Device deleted!"; 
	header('location: device_operation_index.php');
}
?>