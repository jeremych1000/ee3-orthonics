<html>
<head>
<title>Select Records</title>
<link rel="icon" type="image/ico" href="favicon.ico">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script type="text/javascript">
	$(function() {
    	$("#datepicker").datepicker({ dateFormat: "yy-mm-dd" }).val();
    	//$("#datepicker").datepicker('setDate', new Date());
    });
</script>

</head>
<body>

<?php
require_once 'db_functions.php';
$db = db_connect();

//regex used to prune non alphanumeric characters
$iata = db_select("SELECT DISTINCT `iata` FROM `airlines` WHERE `iata` REGEXP '^[A-Za-z0-9]+$' ORDER BY `iata` ASC;");
if($iata === false){
	$error = db_error();
		die('There was an error running the query [' .$error. ']');
}

$airports = db_select("SELECT DISTINCT `iata` FROM `airports` WHERE `iata` REGEXP '^[A-Za-z0-9]+$' ORDER BY `iata` ASC;");
if($airports === false){
	$error = db_error();
		die('There was an error running the query [' .$error. ']');
}

//my_print($icao);

?>

<p>Please use the <b>IATA</b> convention over the <b>ICAO</b> convention. An ICAO->IATA converter can be found <a href="http://www.airlinecodes.co.uk/aptcodesearch.asp">here</a>. ICAO <i>is</i> supported but disabled. Sorry for the inconvenience.</p>
<div id="flight">
<form action="select_records.php" method="post">

	Airline (IATA): 
	<select name="airline">
	<option value=""></option>
	<?php foreach($iata as $option) : ?>
	    <option value="<?php echo $option['iata']; ?>"><?php echo $option['iata']; ?></option>
	<?php endforeach; ?>
	</select>

	<br />

	Flight Number: <input type="text" name="flight_no" min="0" max="9999" placeholder="0-9999" />

	<br />

	Start/End (IATA): 
	<select name="start">
	<option value=""></option>
	<?php foreach($airports as $option) : ?>
	    <option value="<?php echo $option['iata']; ?>"><?php echo $option['iata']; ?></option>
	<?php endforeach; ?>
	</select>

	<select name="end">
	<option value=""></option>
	<?php foreach($airports as $option) : ?>
	    <option value="<?php echo $option['iata']; ?>"><?php echo $option['iata']; ?></option>
	<?php endforeach; ?>
	</select>

	<br />

	Tail ID: <input type="text" name="tail_id" placeholder="5 characters (excl. hyphen)" />

	<br />

	Date: <input type="text" name="date" id="datepicker" placeholder="YYYY-MM-DD" />

	<br /><br />
	<input type="submit" name="submit" value="Get Results" /> <br />

</form>
</div>
</body>


<footer>
Airport and airline data provided by <a href="http://openflights.org/">http://openflights.org/</a>
</footer>

</html>
