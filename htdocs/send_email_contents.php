<html>
<body>
<?php

$f_id = @$_GET['flight_id'];
$i_id = @$_GET['image_id'];
//Replace the plain text body with one created manually
echo 'This email has been generated automatically, please do not reply.';
echo '<br><br>';
echo 'There has been an attack on an aircraft.';
echo '<br>';
echo 'You have a new localisation report which can be found at this link:';
echo '<br>';
echo 'http://orthonics.zapto.org/create_report.php?flight_id='.$f_id.'&image_id='.$i_id;

?>
</body>
</html>
