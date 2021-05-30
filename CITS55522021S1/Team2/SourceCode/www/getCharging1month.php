<?php

	ini_set('display_errors', 1);
	date_default_timezone_set('Australia/Perth');
	require('../private_html/db.php');

#Connect to server
$curl = curl_init();

#Get data from ocpp16 revproject
function getChargeData(){
    
    #get the times for the API call in ISO8601, without timezones
    $totime = substr(date('c'), 0, -6);
    $fromtime = substr(date('c', strtotime('-1 month')),0,-6);

    global $curl,$authHeader,$db;
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://ocpp16.therevproject.com/api/transactions/?from=$fromtime&to=$totime",
      #CURLOPT_URL => "https://ocpp16.therevproject.com/api/transactions/?from=2021-03-15T02:00:00&to=2021-05-15T12:00:00",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);
    $responsearray = (array)$response;
    #print_r($responsearray);
    $resultarray = array();
    $allstamps = array();
    
    #get each 5 minutes within the timerange given
    foreach($responsearray as &$transaction){
        $interval = 5; // minutes
        $start = roundToNearestMinuteInterval(strtotime($transaction["start_timestamp"]), $interval);
        $end = roundToNearestMinuteInterval(strtotime($transaction["end_timestamp"]), $interval);
        $count = 0; 
        
        #split the transactions start to end time into 5 minute intervals,
        #keep track of all timestamps seen for every transaction
        for(; $start <= $end; $start += $interval * 60) {
            if(in_array(date('Y-m-d H:i:s', $start),$allstamps) == False){
                $count++;
                $allstamps[] = date('Y-m-d H:i:s', $start);
                $resultarray[]["timestamp"] = date('Y-m-d H:i:s', $start);
            }
        }
        
        #for each resulting 5 minutes, split the corresponding charging totals among their 5 minute intervals
        $sql_checkquery = "SELECT device_name_ext FROM ems_transaction_ext_table WHERE ref_ext = '$transaction[id]'";    
	    $stmt = $db->query($sql_checkquery);
	    if($stmt === false)
            {
            print_r($db->errorInfo());
            $success = False;
            }
            else
            {
            $success = True;
            }
	    if($stmt->rowCount() > 0){    
	        #ID exists already
	        print("ID: $transaction[id] already exists... <br />");		    
            } 
        else{
            #ID does not exist, creating
            print("ID: $transaction[id] does not exist, creating... <br />");
            foreach($resultarray as &$result){
                if(is_null($result["id"])){
                    $result["id"] = $transaction["id"];
                    }
                $result["name"] = "car charge";
                $result["attribute_name_ext"] = "kWh";
                if($result["id"] == $transaction["id"]){
                    $result["value"] = ($transaction["meter_used"]/$count);
                    }
                    
                }
            }
    }
    return $resultarray;
};


#INSERT INTO ems_transaction_ext_table
function sendChargeData($arraydata){

	global $db;
    foreach($arraydata as &$sendString){
        if($sendString['value'] != 0){
            $sql_query = "INSERT INTO ems_transaction_ext_table(timestamp,value,device_name_ext,attribute_name_ext,ref_ext) VALUES ('$sendString[timestamp]','$sendString[value]','$sendString[name]','$sendString[attribute_name_ext]','$sendString[id]')";    
			$stmt = $db->query($sql_query);	
            if($stmt === false)
                {
                #die('Errors: ' . print_r(sqlsrv_errors(), TRUE));
                print_r($db->errorInfo());
                $success = False;
                }
                else
                {
                $success = True;
                #echo '<br clear="all"><label style="color:red;">Thank you for your details.</label>';       
                }
            }
    }
    print("sending finished");
    return $success;
}

function roundToNearestMinuteInterval($time, $interval) {
    $rounded = round($time / ($interval * 60), 0) * $interval * 60;
    return $rounded;
}

#minute rounding function
$myarray = getChargeData();
print_r($myarray);
print("starting send");
$carsent = sendChargeData($myarray);
echo $carsent;



