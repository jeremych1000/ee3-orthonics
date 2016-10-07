<?php

function searchForId($id, $array) {
   foreach ($array as $key) {
       if (@empty($key[$id]) === false) {
           return $key;
       }
   }
   return null;
}

function get_addr($url){
	$request = file_get_contents($url);
	$json = json_decode($request, true);
	//echo '<pre>';
	//var_dump($json);
	//only get the first addresss for now
	//put empty check else returning empty array, causing 'Notice: Undefined offset: 0 in' error

	//$id = searchForId('formatted_address', $json);
	//var_dump($id);//return $json[$id];
	return (empty($json)) ? 'No address found' : $json['results'][0]['formatted_address']; 
}

function get_circle_coord($Lat,$Lng,$Rad,$Detail=8){
	$R    = 6371;
 
	$pi   = pi();
 
	$Lat  = ($Lat * $pi) / 180;
	$Lng  = ($Lng * $pi) / 180;
	$d    = $Rad / $R;
 
	$points = array();
	$i = 0;
 
	for($i = 0; $i <= 360; $i+=$Detail):
		$brng = $i * $pi / 180;
 
		$pLat = asin(sin($Lat)*cos($d) + cos($Lat)*sin($d)*cos($brng));
		$pLng = (($Lng + atan2(sin($brng)*sin($d)*cos($Lat), cos($d)-sin($Lat)*sin($pLat))) * 180) / $pi;
		$pLat = ($pLat * 180) /$pi;
 
		$points[] = array($pLat,$pLng);
	endfor;
 
	require_once('polyline_encoder.php');
	$PolyEnc   = new PolylineEncoder($points);
	$EncString = $PolyEnc->dpEncode();
 
	return $EncString['Points'];
}

function get_distance($lat1, $lon1, $lat2, $lon2, $decplaces = 2){
	//uses Haversine formula
	$R = 6371e3; //radius of earth in m
	
	$dlon = $lon2 - $lon1;
	$dlat = $lat2 - $lat1;

	$a = (sin($dlat/2))**2 + cos($lat1) * cos($lat2) * (sin($dlon/2))**2;
	$c = 2 * atan2(sqrt($a), sqrt(1-$a));

	$result = $R * $c;
	return round($result, $decplaces);
}

//faster? http://stackoverflow.com/questions/27928/alculate-distance-between-two-latitude-longitude-points-haversine-formula
//returns metres
function get_distance2($lat1, $lon1, $lat2, $lon2, $decplaces = 2) {
	$p = 0.017453292519943295;    // Math.PI / 180
	$a = 0.5 - cos(($lat2 - $lat1) * $p)/2 + 
					cos($lat1 * $p) * cos($lat2 * $p) * 
					(1 - cos(($lon2 - $lon1) * $p))/2;

	$result = 12742e3 * asin(sqrt($a)); // 2 * R; R = 6371 km
	return round(abs($result), $decplaces);
}

function create_distance_array($in){
	$distance = array();
	$distance[] = 0;

	static $lat_old;
	static $lon_old;

	$lat_old = $in[0]["gps_lat"];
	$lon_old = $in[0]["gps_long"];

	for($i = 1; $i < count($in); $i++):
		$lat = $in[$i]["gps_lat"];
		$lon = $in[$i]["gps_long"];
		$distance[] = get_distance2($lat_old, $lon_old, $lat, $lon);
		$lat_old = $lat;
		$lon_old = $lon;
	endfor;

	return $distance;
}

function group_results($in, $threshold = 50){
	$grouped = array();
	//$grouped[] = array();
	//$grouped[0][0] = 0;
	//var_dump($in);
	//var_dump($grouped);
	//echo count($in);
	//echo '<br>';
	//echo count($grouped);
	//echo '<br><br><br>';

	for($i = 0; $i < count($in); $i++):
		//echo 'index i = '.$i.' of '.count($in).'=> '.$in[$i].'<br>';
		$add = false;

		if($i === 0){
			$grouped[] = array();
			$grouped[0][0] = $in[$i];
		}
		else{ 
			for ($j = 0; $j < count($grouped); $j++):
				//echo 'index j = '.$j.' of '.count($grouped).' = '.$grouped[$j][0].'<br>';
				$outside = $in[$i];
				$inside = $grouped[$j][0];
				//echo '<pre />';
				//var_dump($outside);
				//var_dump($inside);
				$diff = abs($outside - $inside);
				//echo 'Outside = '.$outside.'; Inside = '.$inside.'; Diff = '.$diff.'<br>';
				if ($diff <= $threshold){
					//echo 'Append inside <br>';
					$grouped[$j][] = $in[$i];
					$add = true;
				}
			endfor;
			if ($add === false){
				//echo 'Append outside <br>';
				$grouped[] = array($in[$i]);
			}
		}
		//echo 'Dumping $grouped:'.'<br><pre>';
		//var_dump($grouped);
		//echo '</pre><br>';
		////echo 'val at i='.$i.': '.$grouped[$i][0];
		//echo '<br><br><br>';
	endfor;

	return $grouped;
}


?>
