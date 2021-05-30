<?php

	ini_set('display_errors', 1);
	date_default_timezone_set('Australia/Perth');
	require('../private_html/db.php');

#API IDs
$powersid = 13362533; #B222.Ground.MSSB_222_1_NonEssMeter.activePower.kW
$powereid = array(137813805,138512969,139013189,139413585,139013189,139713541); #B275.GROUND.NONESSMETER.ACTIVEPOWER
$airdensitysid = 178715214; #B275.Level04.WeatherStation.airDensityActual.kg/m³
$radiationsid = 178715140; #B275.Level04.WeatherStation.globalRadiationActual.value
$windspeedsid = 178715158; #B275.Level04.WeatherStation.windSpeedActual(m/s).m/s
$solarid = 182016079;
$solareid = array(182116035);

#Connect to server & get token
$tokenQuery= "SELECT token FROM ems_access_table WHERE id = '1'";
$token = 'Authorization: Bearer ';
$stmt = $db->query($tokenQuery);	
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
while ($row = $stmt->fetch()) {
    $token .= $row['token'];
    }
    
#Paste for token for ease
#$token ='Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJJQk1TIFB0eSBMdGQiLCJpc2F0IjoiMTYxODkyMjUzMiIsImV4cCI6IjE2MTkwMDg5MzIiLCJzdWIiOiI0IiwibmFtZSI6InV3YUFQSSIsInVzZXJuYW1lIjoidW5pd2FcXDIxMzAwNjc0IiwiZW1haWwiOiIiLCJkYXNoYm9hcmQiOiIxIiwidHJlbmRpbmciOiIxIiwicm9sZSI6InVzZXIifQ.PjLXfrSbmOwK9KVP-hLyui5RqvM1C9epMmHyGcwg8is';

#Query with token
$authHeader = array($token,'Cookie: BIGipServer~Digital-Twin~digital-twin-http_pool=170066442.20480.0000');
$curl = curl_init();

#getting API Name and Measurement of IDs
function getNameMeasure($id){
    
    global $curl,$authHeader;
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://i.digitaltwin.ezone.uwa.edu.au//api/trendableObject/get?id=$id",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $authHeader,
    ));
    $response = curl_exec($curl);
    #curl_close($curl);
    $response = (array)(json_decode($response));
    $idname = $response['name'];
    $namearray = explode(".",$idname);
    $idmeasure = array_pop($namearray);
    
    $resultarray = array($idname,$idmeasure);
    return $resultarray;
    };



#Get TrendableData call of ID 
function getData($id, $idname,$idmeasure){

    global $curl,$authHeader;
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://i.digitaltwin.ezone.uwa.edu.au//api/trendableData/get?id=$id&from=now-1_month&to=now",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $authHeader,
    ));
    $response = curl_exec($curl);
    #curl_close($curl);
    $response = json_decode($response, true);
    $response = (array)$response;
    $responsedata = $response["$id"];
    
    #FORMAT DATETIME AND ADD NAME COLUMN
    foreach($responsedata as &$dateString){
        $dateString["name"] = $idname;
        $dateString["attribute_name_ext"] = $idmeasure;
    }
    return $responsedata;
};

#Total the Extra IDs and add them to a main array
function totaleid($arraydata, $eid){
    #this totals the extra power ids into the main_array
    global $curl,$authHeader;
    foreach($eid as $newid){
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://i.digitaltwin.ezone.uwa.edu.au//api/trendableData/get?id=$newid&from=now-1_month&to=now",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => $authHeader,
        ));
        $response = curl_exec($curl);
        #curl_close($curl);
        $response = json_decode($response, true);
        $response = (array)$response;
        $newdata = $response["$newid"];
        foreach($newdata as $index => $newestdata){
            if($arraydata[$index]["timestamp"] == $newestdata["timestamp"]){
            $arraydata[$index]["value"] += $newestdata["value"];
            }
        }
    }
    return $arraydata;
    
    };


#INSERT INTO ems_transaction_ext_table
function sendData($arraydata){
	global $db;
    foreach($arraydata as &$sendString){
        $sql_checkquery = "SELECT value FROM ems_transaction_ext_table WHERE timestamp = '$sendString[timestamp]' AND attribute_name_ext = '$sendString[attribute_name_ext]'";    
		    $stmt = $db->query($sql_checkquery);
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
		    if($stmt->rowCount() > 0){    
		        #entry exists already
		        print("Entry at $sendString[timestamp] already exists");    
            } 
            else {
                #entry does not exist
                print("Entry $sendString[timestamp] does not exist, creating...");
                $sql_query = "INSERT INTO ems_transaction_ext_table(timestamp,value,device_name_ext,attribute_name_ext) VALUES ('$sendString[timestamp]','$sendString[value]','$sendString[name]','$sendString[attribute_name_ext]')";    
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
    return $success;
    }


#Power
$powernamearray = getNameMeasure($powersid);
#$powerName = $powernamearray[0];
#$powerMeasure = $powernamearray[1];
$powerName = "NonEssentialPowerMeterTotal";
$powerMeasure = "kW";
print("Getting power");
$powerArray = getData($powersid,$powerName,$powerMeasure);
$totalPowerArray = totaleid($powerArray,$powereid);
print("Sending power");
$powerSent = sendData($totalPowerArray);
echo $powerSent;

#Windspeed
$windnamearray = getNameMeasure($windspeedsid);
#$windName = $windnamearray[0];
#$windMeasure = $windnamearray[1];
$windName = "weatherstation";
$windMeasure = "m/s";
print("Getting wind");
$windArray = getData($windspeedsid,$windName,$windMeasure);
print("Sending wind");
$windSent = sendData($windArray);
echo $windSent;

#airDensity
$airnamearray = getNameMeasure($airdensitysid);
#$airName = $airnamearray[0];
#$airMeasure = $airnamearray[1];
$airName = "weatherstation";
$airMeasure = "kg/m^3";
print("Getting airdens");
$airArray = getData($airdensitysid,$airName,$airMeasure);
print("Sending airdensity");
$airSent = sendData($airArray);
echo $airSent;

#radiation
$radiationnamearray = getNameMeasure($radiationsid);
#$radiationName = $radiationnamearray[0];
#$radiationMeasure = $radiationnamearray[1];
$radiationName = "weatherstation";
$radiationMeasure = "W/m^2";
print("Getting rad");
$radiationArray = getData($radiationsid,$radiationName,$radiationMeasure);
print("Sending radiation");
$radiationSent = sendData($radiationArray);
echo $radiationSent;

#solardata
$solarnamearray = getNameMeasure($solarid);
$solarName = "Solar Meter";
$solarMeasure = "kWh";
print("Getting Solar Energy");
$solarArray = getData($solarid,$solarName,$solarMeasure);
$totalSolarArray = totaleid($solarArray,$solareid);
print("Sending Solar Energy");
$solarSent = sendData($totalSolarArray);
echo $solarSent;

