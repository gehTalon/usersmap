<?php
header('Access-Control-Allow-Origin: *');
$locations = $_POST['data'];

file_put_contents('data.txt', json_encode($locations));
?>