<?php
//add check for flight_id image_id
//define('FPDF_FONTPATH','resources/');
require_once 'fpdf/fpdf.php';
require_once 'db_functions.php';
require_once 'create_report_functions.php';

class PDF extends FPDF
{
    // Page header
	function Header()
	{
	    // Logo
	    $this->Image('resources/laser_wikipedia.png',10,10,-550,-700);
	    $this->SetFont('AGP','BU',20);
	    // Move to the right
	    $this->Cell(80);
	    // Title
	    $this->Cell(30,10,'Orthonics Localisation Report',0,0,'C');
	    // Line break
	    $this->Ln(10);
	}

	// Page footer
	function Footer()
	{
	    // Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('AGP','',6);
	    // Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

//check if flight ID or image ID exists, if not, exit
$f_id = @$_GET["flight_id"];
$i_id = @$_GET["image_id"];

if (!isset($f_id) OR !isset($i_id)){
	die('Not enough parameters.');
}
else{
	if(is_numeric($f_id) === false) {die('Flight ID not numeric.');}
	if(is_numeric($i_id) === false) {die('Image ID not numeric.');}
}

//prevent injection
$f_id = intval($f_id);
$i_id = intval($i_id);

$db = db_connect();

$result = db_select("SELECT * FROM `flight` WHERE `flight_id`=".$f_id.";");
$result2 = db_select("SELECT * FROM `images` WHERE `image_id`=".$i_id." AND `flight_id`=".$f_id.";");
if($result === false || $result2 === false){
	$error = db_error();
		die('There was an error running the query [' .$error. ']');
}

if (empty($result) && !empty($result2))
{
	exit('No such flight ID.');
}
else if (!empty($result) && empty($result2))
{
	exit('No such image ID or flight ID does NOT match.');
}
else if (empty($result) && empty($result2))
{
	exit('No such flight and image ID.');
}
else
{
	$curr_time = date("Y-m-d H:i:s"); 

	//my_print($result);

	//flight table var
	$f_airline = $result[0]['airline'];
	$f_flight_no = $result[0]['flight_no'];
	$f_start = $result[0]['start'];
	$f_end = $result[0]['end'];
	$f_tail_id = $result[0]['tail_id'];
	$f_date = $result[0]['date'];

	//image table var
	$i_time = $result2[0]['time'];
	$i_gps_lat = $result2[0]['gps_lat'];
	$i_gps_long = $result2[0]['gps_long'];

	if (((float)$i_gps_lat < -90) OR ((float)$i_gps_lat > 90)){die('GPS Latitude not in range of -90 to 90.');}
	if (((float)$i_gps_long < -180) OR ((float)$i_gps_long > 180)){die('GPS Longitude not in range of -180 to 180.');}
    //echo '<pre />';
    //var_dump($result2);
	$i_altitude = $result2[0]['altitude'];
	$i_altitude_unit = 'm';
	$i_confidence = $result2[0]['confidence'];
	if (empty($i_confidence)) $i_confidence = '1'; //polyline encoder can't have 0 radius
	$i_confidence_unit = 'm';
	$i_img_link = $result2[0]['img_link'];
	if (empty($i_img_link) OR !file_exists($i_img_link)) $i_img_link = 'resources/noimage.png';

	//gmaps static img var
	$g_api_link = 'https://maps.googleapis.com/maps/api/staticmap?';
	$g_zoomout_factor = '13';
	$g_zoomin_factor = '17';
	$g_marker_colour = 'red';
	$g_map_type = 'roadmap';	//roadmap, hybrid, satellite, terrain
	$g_map_fill   = 'ff0000';    // fill colour of our circle
	$g_map_border = '000000';    // border colour of our circle
	$g_size = '300x300';
	$g_scale = '2'; //1 - regular quality, 2 - higher
	$g_format = 'png8';
	$g_api_key = 'AIzaSyAa6ZK81sMBK2dVrGcOVaWhKUVyfvyUft8';
	/* create our encoded polyline string */
	$g_encstring = get_circle_coord($i_gps_lat, $i_gps_long, $i_confidence/1000); //div1000 as confidence level in KM

	$g_zoomout = $g_api_link.'center='.$i_gps_lat.','.$i_gps_long.'&zoom='.$g_zoomout_factor.'&size='.$g_size.'&maptype='.$g_map_type.'&markers=color:'.$g_marker_colour.'%%7C'.$i_gps_lat.','.$i_gps_long.'&path=fillcolor:0x'.$g_map_fill.'33%7Ccolor:0x'.$g_map_border.'00%7Cenc:'.$g_encstring.'&key='.$g_api_key.'&scale='.$g_scale.'&format='.$g_format.'#.png';
	$g_zoomin = $g_api_link.'center='.$i_gps_lat.','.$i_gps_long.'&zoom='.$g_zoomin_factor.'&size='.$g_size.'&maptype='.$g_map_type.'&markers=color:'.$g_marker_colour.'%%7C'.$i_gps_lat.','.$i_gps_long.'&path=fillcolor:0x'.$g_map_fill.'33%7Ccolor:0x'.$g_map_border.'00%7Cenc:'.$g_encstring.'&key='.$g_api_key.'&scale='.$g_scale.'&format='.$g_format.'#.png';
    //echo $g_zoomout;
	
	$pdf = new PDF();
	$pdf->SetTitle($f_airline.' '.$f_flight_no.' '.$i_time);
	//find number of pages
	$pdf->AliasNbPages();

    $pdf->AddFont('AGP','','d63f40040fa07a158fd80cf53a58e21b_agaramondpro-regular.php');
    $pdf->AddFont('AGP','B','9a3bd72734e2b51984700b84873ff6b2_agaramondpro-bold.php');

    $my_font = 'AGP';

	$pdf->AddPage();
	$pdf->SetFont($my_font, '', 10);
	$pdf->Cell(80,20);
	$pdf->Cell(30,10,'Dynamically generated on '.$curr_time,0,0,'C');

	$pdf->Ln(20);

	$pdf->SetFont($my_font, 'B', 12);
	$pdf->SetXY(10,30);
	$pdf->Cell(40,10,'Flight Details:',0,0,'L');

	$pdf->SetFont($my_font, '', 12);
	$pdf->Ln(7);
	$pdf->Cell(40,10,'Airline: '.$f_airline,0,0,'L');
	$pdf->Ln(7);
	$pdf->Cell(40,10,'Flight Number: '.$f_flight_no,0,0,'L');
	$pdf->Ln(7);
	$pdf->Cell(40,10,'Start: '.$f_start,0,0,'L');
	$pdf->Ln(7);
	$pdf->Cell(40,10,'End: '.$f_end,0,0,'L');
	$pdf->Ln(7);
	$pdf->Cell(40,10,'Tail ID: '.$f_tail_id,0,0,'L');
	$pdf->Ln(7);
	$pdf->Cell(40,10,'Date: '.$f_date,0,0,'L');

	$pdf->SetFont($my_font, 'B', 12);
	$pdf->SetXY(100,30);
	$pdf->Cell(40,10,'Image Details:',0,0,'L');

	$pdf->SetFont($my_font, '', 12);
	$pdf->Ln(7); $pdf->SetX(100);
	$pdf->Cell(40,10,'Time Taken: '.$i_time,0,0,'L');
	$pdf->Ln(7); $pdf->SetX(100);
	$pdf->Cell(40,10,'GPS Latitude: '.$i_gps_lat,0,0,'L');
	$pdf->Ln(7); $pdf->SetX(100);
	$pdf->Cell(40,10,'GPS Longitude: '.$i_gps_long,0,0,'L');
	$pdf->Ln(7); $pdf->SetX(100);
	$pdf->Cell(40,10,'Altitude: '.$i_altitude.' '.$i_altitude_unit,0,0,'L');
	$pdf->Ln(7); $pdf->SetX(100);
	$pdf->Cell(40,10,'Confidence Level: '.$i_confidence.' '.$i_confidence_unit,0,0,'L');
	$pdf->Ln(7); $pdf->SetX(100);

	$pdf->SetFont($my_font, 'B', 12);
	$pdf->SetXY(10,85);
	$pdf->Cell(40,10,'Estimated Address:',0,0,'L');

	$pdf->SetFont($my_font, '', 12);
	$pdf->SetX(50);
	$addr_url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$i_gps_lat.','.$i_gps_long;
    //echo $addr_url;
    //echo '<pre>';
    //var_dump(get_addr($addr_url));
	$pdf->MultiCell(140,10,get_addr($addr_url),0,'L');

	$pdf->SetFont($my_font, 'B', 12);
	$pdf->SetXY(10,100);
	$pdf->Cell(40,10,'Evidence:',0,0,'L');
	$pdf->Image($i_img_link,10,110,'',65);

	$pdf->SetFont($my_font, 'B', 12);
	$pdf->SetXY(10,180);
	$pdf->Cell(40,10,'Google Maps of coordinates:',0,0,'L');
	$pdf->Ln(7);
	$pdf->Image($g_zoomout,10,190,-165,-165,'PNG');
	$pdf->Image($g_zoomin,110,190,-165,-165,'PNG');

	$pdf->Output();

}
?>