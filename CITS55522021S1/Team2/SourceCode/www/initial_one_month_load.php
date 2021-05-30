<?php
	require('../private_html/db.php');

?>


<?php
	$sql = "select pvsystem,device_name,attribute_name,unit,timestamp,unit,value from ems_transaction_view order by timestamp desc limit 20520;"; //query for selecting all data from table from past one month which can be later modified based on required frequencies
	$result = $db->query($sql);

	$json_array = array();
		
	if($result->rowCount()) {//loop to fetch the data from sql
		$row = $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT);
		while($row = $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT)){
			$test_data[]=$row;
		}
		$json['testData']=$test_data;
		
		
	}	
		
	echo json_encode($json, 128); //converting array into json format 
?>