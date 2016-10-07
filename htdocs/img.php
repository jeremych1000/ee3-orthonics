<html>
<head>
<title>Web Image Upload</title>
<link rel="icon" type="image/ico" href="favicon.ico">
</head>
<body>

<?php
$image_id = @$_GET["image_id"];

if (!isset($image_id)) {
	die ("No image ID provided.");
}
else {
	if(is_numeric($image_id) === false){
		die('Non numeric image ID provided.');
	}
	else{
		$image_id = intval($image_id);
	}
}
?>

<form action="img_upload.php" method="post" enctype="multipart/form-data">

<input type="file" name="image" /> 
<input type='hidden' name="image_id" value="<?php echo "$image_id"; ?>" />
<input type="submit" name="submit" value="Upload" /> <br />

</form>

</body>

</html>