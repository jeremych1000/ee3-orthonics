<html>
<head>
<title>API FAQ</title>
<link rel="icon" type="image/ico" href="favicon.ico">
</head>
<body>
<p> 
3 step process to use API:
<ol>
<li type="1">
Add flight and image records using the parameters below. Add flight record first. Make a note of returned <b>flight_id</b> and use it in image record's <b>flight_id</b>. Make a note of returned <b>image_id</b>.
</li>
<li type="1">
Upload the evidence image (PNG/JPG) using the returned <b>image_id</b>.
</li>
<li type="1">
Create police report by using <b>flight_id</b> and <b>image_id</b> parameters.
</li>
</p>

<br /><br />

<ul>
<li>
<p>
<b>Add flight record (5 required, 1 optional parameters: type, airline, flight_no, start, end, date, (tail_id)) </b>
<br />
[returns flight_id (int)]
<br /><br />
start/end 3 char, tail id 2 char - 3 char, date YYYY-MM-DD
<br />
<a href="http://orthonics.zapto.org/add_record.php?type=flight&airline=TA&flight_no=433&start=AYQ&end=MBI&tail_id=AS-DTU&date=2016-05-15">http://orthonics.zapto.org/add_record.php?type=flight&airline=TA&flight_no=433&start=AYQ&end=MBI&tail_id=AS-DTU&date=2016-05-15</a>
</p>
</li>

<br />

<li>
<p>
<b>Add image record (5 required, 2 optional parameters: type, flight_id, gps_lat, gps_long, time, (altitude), (confidence) </b>
<br />
[returns image_id (int)]
<br /><br />
%20 means space, timestamp YYYY-MM-DD %20 HH:MM:SS, altitude and confidence in metres
<br />
<a href="http://orthonics.zapto.org/add_record.php?type=image&flight_id=3&gps_lat=51.4986496&gps_long=-0.1748340&time=2016-05-16%2016:19:03&altitude=123.2&confidence=20">http://orthonics.zapto.org/add_record.php?type=image&flight_id=3&gps_lat=51.4986496&gps_long=-0.1748340&time=2016-05-16%2016:19:03&altitude=123.2&confidence=20</a>
</p>
</li>

<br />

<li>
<p>
<b>Upload image here (PNG/JPG/JPEG only) using multipart/form-data (1 required parameter: image_id)</b>
<br />
[returns 'success' if success (string)]
<br /> <br />
Web based image upload: 
<a href="http://orthonics.zapto.org/img.php?image_id=20">http://orthonics.zapto.org/img.php?image_id=20</a>
<br />
Direct POST (include image encoded as multpart/form-date using POST, with correct image_id_direct in URL): 
<a href="http://orthonics.zapto.org/img_upload.php?image_id_direct=20">http://orthonics.zapto.org/img_upload.php?image_id_direct=20</a>
</p>
</li>

<br />

<li>
<p>
<b>Create police report (2 required parameters)</b>
<br />
[returns PDF]
<br /><br />
flight_id of image record must match flight_id of flight record
<br />
<a href="http://orthonics.zapto.org/create_report.php?flight_id=1&image_id=1">http://orthonics.zapto.org/create_report.php?flight_id=1&image_id=1</a>
</p>
</li>

<br />

<li>
<p>
<b>Extra: Show all database records (no parameters)</b>
<br />
<a href="http://orthonics.zapto.org/show_records.php">http://orthonics.zapto.org/show_records.php</a>
</p>
</li>


<br />

<li>
<p>
<b>Extra: Select records by columns (no parameters)</b>
<br />
<a href="http://orthonics.zapto.org/select.php">http://orthonics.zapto.org/select.php</a>
</p>
</li>


</ul>
</body>

</html>