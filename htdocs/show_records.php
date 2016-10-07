<html>
<head>
<title>
	Show all records
</title>
<link rel="icon" type="image/ico" href="favicon.ico">
</head>
<header>
<script src="js/sorttable.js"></script>
<script type="text/javascript">
	// append column to the HTML table
	//http://www.redips.net/javascript/adding-table-rows-and-columns/
function appendColumn() {
    var tbl = document.getElementById('images'), // table reference
        i;
    //gen title
    createCell(tbl.rows[0].insertCell(tbl.rows[0].cells.length), 'Generate Report', 'col');
    // open loop for each row and append cell
    for (i = 1; i < tbl.rows.length; i++) {
        createCell(tbl.rows[i].insertCell(tbl.rows[i].cells.length), i, 'col');
    }
}

// create DIV element and append to the table cell
function createCell(cell, text, style) {
    var div = document.createElement('div'), // create DIV element
        txt = document.createTextNode(text); // create text node
    div.appendChild(txt);                    // append text node to the DIV
    div.setAttribute('class', style);        // set DIV class attribute
    div.setAttribute('className', style);    // set DIV class attribute for IE (?!)
    cell.appendChild(div);                   // append DIV to the table cell
}

//window.onload = appendColumn;
</script>

<style>
table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
    content: " \25B4\25BE" 
}
</style>

</header>

<body>

	<?php
	//AIzaSyAa6ZK81sMBK2dVrGcOVaWhKUVyfvyUft8
	require_once 'db_functions.php';
    $limit = @$_GET['limit'];
	$db = db_connect();

	if(empty($limit)){
        $result = db_select("SELECT * FROM `flight` ORDER BY `flight_id` ASC;");
    }
    else{
        $result = db_select("SELECT * FROM (SELECT * FROM `flight` ORDER BY `flight_id` DESC LIMIT $limit) T ORDER BY 'flight_id' ASC;");
    }

	if($result === false){
		$error = db_error();
			die('There was an error running the query [' .$error. ']');
	}

	if(empty($limit)){
        $result2 = db_select("SELECT * FROM `images` ORDER BY `image_id` ASC;");
    }
    else{
        $result2 = db_select("SELECT * FROM (SELECT * FROM `images` ORDER BY `image_id` DESC LIMIT $limit) T ORDER BY 'image_id' ASC;");
    }
	if($result2 === false){
		$error = db_error();
			die('There was an error running the query [' .$error. ']');
	}
	?>

	<p />Showing all records 
	(<?php echo sizeof($result); ?>)
	in the flight table ...
	<br /><br />

	<table id="flights" class="sortable" border='1'>
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


	<p />Showing all records (<?php echo sizeof($result2); ?>)
	in the images table ...
	<br /><br />

	<table id="images" class="sortable" border='1'>
	<tr>
	<th />Flight ID
	<th />Timestamp
	<th />GPS Latitude
	<th />GPS Longitude
	<th />Altitude
	<th />Confidence
	<th />Image Link
	<th />Image ID (primary)
	</tr>
	<?php array_walk($result2, 'print_row'); ?>
	</table>

</body>

</html>