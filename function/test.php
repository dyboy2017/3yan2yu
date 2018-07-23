<?php 
 
 	$str = isset($_GET['str'])?$_GET['str']:'';
 	if($str){
 		echo md5($str);
 	}


 ?>