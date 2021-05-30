<?php
	require('../private_html/db.php');
?>


<?php
	session_start();

	// initialize variables
	$attributeName = "";
	$attributeId = "";
	$id = 0;
	$update = false;

	if (isset($_POST['save'])) {
		$attributeName = $_POST['attributeName'];
		$attributeId = $_POST['attributeId'];
 
		$sql = "INSERT INTO ems_attribute_mapping_table (attribute_name_ext, attribute_id) VALUES ('$attributeName','$attributeId')"; 
		$result = $db->query($sql);
		
		
		$_SESSION['message'] = "Attribute mapping saved"; 
		header('location: attribute_mapping_index.php');
	}
	
	if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$attributeName = $_POST['attributeName'];
	$attributeId = $_POST['attributeId'];

	$sql = "UPDATE ems_attribute_mapping_table SET attribute_name_ext='$attributeName', attribute_id='$attributeId' WHERE attribute_id=$id"; 
	$result = $db->query($sql);
	
	$_SESSION['message'] = "Attribute mapping updated!"; 
	header('location: attribute_mapping_index.php');
}

	if (isset($_GET['del'])) {
	$id = $_GET['del'];
	
	$sql = "DELETE FROM ems_attribute_mapping_table WHERE attribute_id=$id"; 
	$result = $db->query($sql);
	$_SESSION['message'] = "Attribute mapping deleted!"; 
	header('location: attribute_mapping_index.php');
}
?>