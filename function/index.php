<?php
	// 获取用户行为
	require_once('../globals.php');
	
	$action = isset($_GET['action']) ? addslashes(trim($_GET['action'])) : '';

?>