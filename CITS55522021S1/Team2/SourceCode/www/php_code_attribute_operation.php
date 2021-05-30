<?php
	require('../private_html/db.php');
?>


<?php
	session_start();

	// initialize variables
	$attributeName = "";
	$unit = "";
	$id = 0;
	$update = false;

	if (isset($_POST['save'])) {
		$attributeName = $_POST['attributeName'];
		$unit = $_POST['unit'];
 
		$sql = "INSERT INTO ems_attribute_table (attribute_name, unit) VALUES ('$attributeName', '$unit')"; 
		$result = $db->query($sql);
		
		
		$_SESSION['message'] = "Attribute saved"; 
		header('location: attribute_operation_index.php');
	}
	
	if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$attributeName = $_POST['attributeName'];
	$unit = $_POST['unit'];

	$sql = "UPDATE ems_attribute_table SET attribute_name='$attributeName', unit='$unit' WHERE attribute_id=$id"; 
	$result = $db->query($sql);
	
	$_SESSION['message'] = "Attribute updated!"; 
	header('location: attribute_operation_index.php');
}

	if (isset($_GET['del'])) {
	$id = $_GET['del'];
	
	$sql = "DELETE FROM ems_attribute_table WHERE attribute_id=$id"; 
	$result = $db->query($sql);
	$_SESSION['message'] = "Attribute deleted!"; 
	header('location: attribute_operation_index.php');
}
?>