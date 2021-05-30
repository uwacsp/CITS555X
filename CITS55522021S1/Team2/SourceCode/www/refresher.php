<?php
	require('../private_html/db.php');
?>


<?php
	header("refresh: 300"); //refreshes every 5 minutes
	$sql = "select ems_transaction_view.* from ems_transaction_view,
           (select device_name,max(timestamp) as timestamp
                from ems_transaction_view
                group by device_name) max_sales
             where ems_transaction_view.device_name=max_sales.device_name
             and ems_transaction_view.timestamp=max_sales.timestamp;"; //query for updating with the latest record
	$result = $db->query($sql);
	
		
	if($result->rowCount()) {//loop to fetch the data from sql
		$row = $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT);
		while($row = $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT)){
			$test_data[]=$row;
		}
		$json['testData']=$test_data;
		
		
	}	
		
	echo json_encode($json, 128); //converting array into json format 
?>