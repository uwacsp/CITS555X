<?php
	ini_set('display_errors', 1);
	date_default_timezone_set('Australia/Perth');
	require('../private_html/db.php');


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getuserpass(){
    global $db;
    $getUserPassQuery="SELECT User,pass FROM ems_access_table WHERE id = '1'";
    $stmt = $db->query($getUserPassQuery);
    if($stmt === false)
        {
        die('Errors: ' . print_r(sqlsrv_errors(), TRUE));
        $success = False;
        }
        else
        {
        $success = True;
        #echo '<br clear="all"><label style="color:red;">Thank you for your details.</label>';       
        }
    return list ( $user, $pass ) = $stmt->fetch( PDO::FETCH_NUM );
}

$uplist = getuserpass();

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://i.digitaltwin.ezone.uwa.edu.au//api/home/getJWT?username=$uplist[0]&password=$uplist[1]",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: BIGipServer~Digital-Twin~digital-twin-http_pool=170066442.20480.0000'
  ),
));

$response = curl_exec($curl);
curl_close($curl);

#$fulltoken = 'Authorization: Bearer ';
$fulltoken = get_string_between($response, "access_token\":\"", "\",\"token_type");
#echo $fulltoken;
        $insertTokenQuery="UPDATE ems_access_table SET token ='$fulltoken' WHERE id = '1'";
        $stmt = $db->query($insertTokenQuery);		
        if($stmt === false)
            {
            die('Errors: ' . print_r(sqlsrv_errors(), TRUE));
            $success = False;
            }
            else
            {
            $success = True;
            #echo '<br clear="all"><label style="color:red;">Thank you for your details.</label>';       
            }
        echo $success;
