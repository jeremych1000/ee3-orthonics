<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'db_functions.php';

$my_type = @$_GET["type"];

if ($my_type == "flight" || $my_type == "flights") 
{
	$airline = @$_GET["airline"];
	$flight_no = @$_GET["flight_no"];
	$start = @$_GET["start"];
	$end = @$_GET["end"];
	$tail_id = @$_GET["tail_id"]; //optional
	$flight_date = @$_GET["date"];

	//optional defaults
	$tail_id = (isset($tail_id)) ? $tail_id : '00-000';

	//type checking
	//$value = true AND false; // outputs true, USE PARENTHESES!!!!!!
	$continue = (isset($airline)) AND (isset($flight_no)) AND (isset($start)) AND (isset($end)) AND (isset($flight_date));
	
	$continue = ($continue AND ctype_alnum($airline));
	if ($continue === false) {die('Airline input not alphanumeric.');}
	$continue = ($continue AND is_numeric($flight_no));
	if ($continue === false) {die('Flight number input not numeric.');}
	$continue = ($continue AND ctype_alnum($start));
	if ($continue === false) {die('Start IATA code input not alphanumeric.');}
	$continue = ($continue AND ctype_alnum($end));
	if ($continue === false) {die('End IATA code not alphanumeric.');}
	$continue = ($continue AND ctype_alnum(str_replace("-","",$tail_id)));
	if ($continue === false) {die('Tail ID not alphanumeric (excluding hyphen).');}
	$continue = ($continue AND validateDate($flight_date, 'Y-m-d'));
	if ($continue === false) {die('Date is not valid. Remember to add zeroes.');}

	if ($continue === true)
	{
		$db = db_connect();
		//prevent sql injection
		$airline = db_quote($airline);
		$flight_no = db_quote(intval($flight_no));
		$start = db_quote($start);
		$end = db_quote($end);
		$tail_id = db_quote($tail_id);
		$flight_date = db_quote($flight_date);

		$query = "INSERT INTO `flight` (`airline`, `flight_no`, `start`, `end`, `tail_id`, `date`) VALUES ('$airline', '$flight_no', '$start', '$end', '$tail_id', '$flight_date') ON DUPLICATE KEY UPDATE `flight_id` = LAST_INSERT_ID(flight_id);;";
		//echo "$query";
		$result = db_query($query);

		if($result === false){
			$error = db_error();
   			die('There was an error running the query [' .$error. ']');
		}
		else
		{
			$last_id = mysqli_insert_id($db);
			//echo "$last_id";
			$new_url = 'http://orthonics.zapto.org/show_text.php?text='.$last_id;
			//header("Location: $new_url");
			die("$last_id"); //http://stackoverflow.com/questions/768431/how-to-make-a-redirect-in-php
			//echo 'Successfully added flight record.'; //todo, return flight id
		}
	}
	else
	{
		die('Not enough parameters specified.');
	}
}
else if ($my_type == "image" || $my_type == "images")
{
	$f_id = $_GET["flight_id"];
	$image_time = $_GET["time"];
    $image_time = urldecode($image_time);
	$gps_lat = $_GET["gps_lat"];
	$gps_long = $_GET["gps_long"];
	//$image_link = $_GET["image_link"];
	$altitude = @$_GET["altitude"]; //optional
	$confidence = @$_GET["confidence"]; //optional

	//optional defaults
	$altitude = (isset($altitude)) ? $altitude : '0';
	$confidence = (isset($confidence)) ? $confidence : '1';

	//type check
	$continue = (isset($f_id)) AND (isset($image_time)) AND (isset($gps_lat)) AND (isset($gps_long));

	$continue = ($continue AND is_numeric($f_id));
	if($continue === false){die('Flight ID not numeric.');}
	$continue = ($continue AND validateDate($image_time)); //default is YYYYMMDDhhmmss
	if($continue === false){die('Timestamp not valid. Remember to add zeroes and %20 (space).');}
	$continue = ($continue AND is_numeric($gps_lat));
	if($continue === false){die('GPS Latitude not numeric.');}
	//$continue = ($continue AND (($gps_lat < -90) OR ($gps_lat > 90)));
	//if($continue === false){die('GPS Latitude not in range of 0-90.');}
	$continue = ($continue AND is_numeric($gps_long));
	if($continue === false){die('GPS Longitude not numeric.');}
	//$continue = ($continue AND (($gps_long < -90) OR ($gps_long > 180)));
	//if($continue === false){die('GPS Latitude not in range of 0-90.');}
	$continue = ($continue AND is_numeric($altitude));
	if($continue === false){die('Altitude not numeric.');}
	$continue = ($continue AND is_numeric($confidence));
	if($continue === false){die('Confidence level not numeric.');}

	if ($continue === true)
	{
		$db = db_connect();
		//prevent sql injection
		$f_id = db_quote($f_id);
		$image_time = $image_time; //already decoded 
		$gps_lat = (float)$gps_lat;
		$gps_long = (float)$gps_long;
		$altitude = db_quote($altitude);
		$confidence = db_quote($confidence);

		if (($gps_lat < -90) OR ($gps_lat > 90)){die('GPS Latitude not in range of -90 to 90.');}
		if (($gps_long < -180) OR ($gps_long > 180)){die('GPS Longitude not in range of -180 to 180.');}

		$query = "INSERT INTO `images` (`flight_id`, `time`, `gps_lat`, `gps_long`, `altitude`, `confidence`) VALUES ('$f_id', '$image_time', '$gps_lat', '$gps_long', '$altitude', '$confidence') ON DUPLICATE KEY UPDATE `image_id` = LAST_INSERT_ID(image_id);";
        //die($query);
		$result = db_query($query);

		if($result === false){
			$error = db_error();
   			die('There was an error running the query [' .$error. ']');
		}
		else
		{ 
			$last_id = mysqli_insert_id($db);
			$new_url = 'http://orthonics.zapto.org/show_text.php?text='.$last_id;
            //send email
            $email_url = 'http://orthonics.zapto.org/send_email.php?flight_id='.$f_id.'&image_id='.$last_id;
            //echo $email_url;
            //visit website to send the email to authorities
            url_get_contents($email_url);
			//header("Location: $new_url");
			die("$last_id"); //http://stackoverflow.com/questions/768431/how-to-make-a-redirect-in-php
			//echo "$last_id";
			//echo 'Successfully added image record.'; //todo - return image id
		}
	}
	else
	{
		die('Not enough parameters specified.');
	}
}
else
{
	die('Type not specified or not supported.');
}
?>
