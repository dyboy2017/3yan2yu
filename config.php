<?php
/**
 * 全局项加载 - config.php //配置文件
 * @copyright (c) DYCMS All Rights Reserved
 */
error_reporting(1);
header('Content-type: application/json,charset=utf8');
//mysql database address
define('DB_HOST','localhost');
//mysql database user
define('DB_USER','xxxxx');
//database password
define('DB_PASSWD','xxxxx');
//database name
define('DB_NAME','xxxxx');
//database prefix
define('DB_PREFIX','simichat_');
//auth key
define('AUTH_KEY','');
//cookie name
define('AUTH_COOKIE_NAME','');
//当前网站域名
define('DY_ROOT', 'https://chat.dyboy.cn/');
//官方服务域名
define('OFFICIAL_SERVICE_HOST', 'https://chat.dyboy.cn/');
//设置时区
ini_set('date.timezone','Asia/Shanghai');


//连接数据库
$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWD,DB_NAME);
if($mysqli->connect_errno>0){
	$response = [
		"code" => 2002,
		"msg" => "数据库连接失败，请检查账号密码是否正确"
	];
	echo json_encode($response);
	exit();
}
$mysqli->query("SET NAMES UTF8");
