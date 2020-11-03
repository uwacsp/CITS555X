<?php
	include_once '/dbh.inc.php'
?>


<?php
	$sql = "SELECT * from EMS_Transaction_View;"; //query for selecting all data from table
	$result = mysqli_query($conn, $sql);
	$json_array = array();
	
	while($row = mysqli_fetch_assoc($result)) //loop to fetch the data from sql
	{
		$json_array[] = $row;
	}
	
	echo json_encode($json_array); //converting array into json format 
?>