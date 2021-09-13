<?php
 $data = json_decode(file_get_contents('data.txt'), true);
 
 foreach($data as $location)
 {
     $loc[] = array('name'=>$location['name'],'lat'=>$location['lat'],'lng'=>$location['lng']);
 }

 echo json_encode(array("locations"=>$data));
?>