<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'db_functions.php';

//mysql_* is deprecated, use mysqli instead
$db = db_connect();

//////////////////////////////////////
$image_id = @$_POST["image_id"];
if (!isset($image_id)) {
	$image_id_direct = @$_GET["image_id_direct"];
	if (!isset($image_id_direct)){
		die ('No image ID provided.');
	}
	if (is_numeric($image_id_direct) === false){die('Non integer image ID provided.');}
	else{$image_id = db_quote(intval($image_id_direct));}
}
else {
	if (is_numeric($image_id) === false){die('Non integer image ID provided.');}
	else{$image_id = db_quote(intval($image_id));}
}

if(isset($_FILES['image'])){
	$errors= array();

	$img_dir = 'img/';

	$file_tmp = $_FILES['image']['tmp_name'];
	$file_name = $_FILES['image']['name'];
	$file_size = $_FILES['image']['size'];
	$file_type = $_FILES['image']['type'];   
	$file_mime = @mime_content_type($file_tmp); //use mime type as html header has no extension
	//$file_ext = strtolower(end(explode('/',$file_ext)));
	//$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

	//allowed extensions
	switch($file_mime){
		case 'image/png':
			$file_ext = 'png';
			break;
		case 'image/jpeg':
			$file_ext = 'jpg';
			break;
		default:
			$file_ext = 'not_allowed'; //http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
			break;
	}
	
	//$extensions= array('jpg','png'); 		
	if($file_ext === 'not_allowed'){
		$errors[]='No file selected, or extension not allowed, please choose a JPEG or PNG (preferred) file.';
		die('No file selected, or extension not allowed, please choose a JPEG or PNG (preferred) file.');
	}

	$max_file_size = 10;
	if(($file_size > $max_file_size*1024*1024) OR ($file_size === 0)){
		$errors[]='File size must be larger than 0 bytes and smaller than '.$max_file_size.' MB.';
		die('File size must be larger than 0 bytes and smaller than '.$max_file_size.' MB.');
	}		
	
	if(empty($errors)===true){
		//UPLOAD AND ENTER INTO DATABASE HERE
		//do{ //check if duplicate file name (should never happen...)
			$hash = get_md5_name();
			$newName = $hash . '.' . $file_ext;
			$fullName = $img_dir.$newName;
		//} while (file_exists($fullName) === true);

		//standardize everything to PNG
		//if($file_ext == 'jpeg' || $file_ext == 'jpg'){
		//	imagepng(imagecreatefromjpeg($file_tmp), $fullName);
		//}

		$move_success = move_uploaded_file($file_tmp, $fullName);

		if ($move_success === true){
			$query = "UPDATE `images` SET `img_link` = '$fullName' WHERE `images`.`image_id` = '$image_id'";
			$result = db_query($query);
			if($result === false){
				$error = db_error();
	   			die('There was an error running the query [' .$error. ']');
			}
			else
			{
				//$last_id = mysqli_insert_id($db);
				//echo 'http://orthonics.zapto.org/' . $newName;
				//echo "$fullName";
				//echo "<br />";
				//echo "$last_id";
				echo 'success';
			}
		}
		else{
			//var_dump($file_tmp);
			//echo $file_ext;
			die('Failed to move file.');
		}
				
		//$query = "INSERT INTO `images` (`img_link`) VALUES ('$fullName') WHERE `images`.`img_id` = $image_id";
		
		$db -> close();
		//$result -> free();
	}
	else{
		print_r($errors);
		die();
	}
}
else{
	echo 'No image in POST request. <br /> Current POST request is: <br />';
	echo '<pre>';
	var_dump($_POST);
	echo '</pre>';
}
?>
