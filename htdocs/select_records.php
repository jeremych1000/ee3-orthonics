<html>
<title>Show records</title>
<header>
	<script src="js/sorttable.js"></script>
	<style>
	table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
	    content: " \25B4\25BE" 
	}
	</style>
</header>
<body>
<?php
require_once 'db_functions.php';

$airline = $_POST["airline"];
$flight_no = $_POST["flight_no"];
$start = $_POST["start"];
$end = $_POST["end"];
$tail_id = $_POST["tail_id"];
$date = $_POST["date"];

if (empty($airline) AND empty($flight_no) AND empty($start) AND empty($end) AND empty($tail_id) AND empty($date)){
	die('Please enter at least one input.');
}
else{
	if(!empty($airline)){
		if (ctype_alnum($airline) === false) {die('Airline input not alphanumeric.');}
	}
	if(!empty($flight_no)){
		if (is_numeric($flight_no) === false) {die('Flight number input not numeric.');}
	}
	if(!empty($start)){
		if (ctype_alnum($start) === false) {die('Start IATA code input not alphanumeric.');}
	}
	if(!empty($end)){
		if (ctype_alnum($end) === false) {die('End IATA code not alphanumeric.');}
	}
	if(!empty($tail_id)){
		if (ctype_alnum(str_replace("-","",$tail_id)) === false) {die('Tail ID not alphanumeric (excluding hyphen).');}
	}
	if(!empty($date)){
		if (validateDate($date, 'Y-m-d') === false) {die('Date is not valid. Remember to add zeroes.');}
	}

	//sanitize to prevent sql injection
	$airline = db_quote($airline);
	$flight_no = db_quote(intval($flight_no));
	$start = db_quote($start);
	$end = db_quote($end);
	$tail_id = db_quote($tail_id);
	$date = db_quote($date);

	$db= db_connect();
	
	$query = 'SELECT * FROM `flight` WHERE ' .
	'1=1' . //need to have something before AND
	(empty($airline) ? '': ' AND `airline` = '.'\''.$airline.'\'') .
	(empty($flight_no) ? '' : ' AND `flight_no` = '.'\''.$flight_no.'\'') .
	(empty($start) ? '' : ' AND `start` = '.'\''.$start.'\'') .
	(empty($tail_id) ? '' : ' AND `end` = '.'\''.$end.'\'') .
	(empty($date) ? '' : ' AND `date` = '.'\''.$date.'\'') .
	' ORDER BY `flight_id` ASC';
	//echo $query;
	$result = db_select($query);
	if($result === false){
		$error = db_error();
			die('There was an error running the query [' .$error. ']');
	}
}
?>

<p>Results in flight table... <br /></p>
<table class="sortable" border='1'>
	<tr>
	<th />Airline
	<th />Flight Number
	<th />Start
	<th />End
	<th />Tail ID
	<th />Date
	<th />Flight ID (primary)
	</tr>
	<?php array_walk($result, 'print_row'); ?>
</table>


</body>
</html>
