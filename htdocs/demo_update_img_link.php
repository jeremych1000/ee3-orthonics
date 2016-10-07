<?php

require_once('db_functions.php');
$db = db_connect();

$filename = @$_GET['filename'];
$i_id = @$_GET['image_id'];
$create_name = @$_GET['create_name'];

if($create_name === '1'){
    $output = get_md5_name();
    die("$output");
}
else if (!isset($filename) OR !isset($i_id)){ 
    die('Provide both name and image_id.');
}
else if (is_string($filename) === false OR is_numeric($i_id) === false){
    die('Type wrong');
}
else{
    $name = db_quote($filename);
    $i_id = db_quote($i_id);
    $img_link = 'img/demo/'.$filename;
    $query = "UPDATE `images` SET `img_link` = '$filename' WHERE `images`.`image_id` = '$i_id'";
    $result = db_query($query);
    if($result === false){
        $error = db_error();
        die('There was an error running the query [' .$error. ']');
    }
    else {
        die('success');
    }
}


?>