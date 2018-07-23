<?php
/*
 * 全局调用文件 globals.php
 * description: 本系统由DYBOY独立编写，期待大家共同完善，共建一个优秀便捷的后端
 * 
 * */
require_once('config.php');

//查询数据库
function api_query($sql){
	$result = $GLOBALS['mysqli']->query($sql) or ( $reponse = [ "code" => 500,"data" => ["msg" => "数据库查询失败"] ] and die(json_encode($reponse)));
    return $result;
}

//json输出
function json_echo($response) {
	header("content-type:text/json;charset=utf8");
	echo json_encode($response);
}

//json输出并结束
function json_echo_exit($response) {
	header("content-type:text/json;charset=utf8");
	echo json_encode($response);
	exit();
}

//输出错误信息
function dy_msg($msgs) {
	echo $msgs;
	exit();
}

/**
 * 页面跳转
 * @param string $directUrl
 */
function dyGoUrl($directUrl) {
	header("Location: $directUrl");
	exit;
}

/**
 * 获取文件后缀名
 * @param string $fileName 文件名
 */
function getFileSuffix($fileName) {
	return strtolower(pathinfo($fileName,  PATHINFO_EXTENSION));
}

/**
 * 生成一个随机的字符串
 * @param int $length
 * @param boolean $special_chars
 */
function getRandStr($length = 12, $special_chars = true) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ($special_chars) { $chars .= '!@#$%^&*()'; }
	$randStr = '';
	for ($i = 0; $i < $length; $i++) {
		$randStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}
	return $randStr;
}
//生成随机数字
function getRandNum($length = 4) {
	$chars = '0123456789';
	$randStr = '';
	for ($i = 0; $i < $length; $i++) {
		$randStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}
	return $randStr;
}

/**
 * 验证email地址格式
 * @param $email
 */
function checkMail($email) {
	if (preg_match("/^[\w\.\-]+@\w+([\.\-]\w+)*\.\w+$/", $email) && strlen($email) <= 60) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取QQ头像
 * @param $qq_num
 * @param $size
 */
function getQQhead($qq_num, $size=3) {
	$avatar_url = "https://q.qlogo.cn/headimg_dl?dst_uin=$qq_num&spec=$size";
	return $avatar_url;
}

/**
 * hmac 加密
 * @param unknown_type $algo hash算法 md5
 * @param unknown_type $data 用户名和到期时间
 * @param unknown_type $key
 * @return unknown
 */
if(!function_exists('hash_hmac')){
	function hash_hmac($algo, $data, $key) {
		$packs = array('md5' => 'H32', 'sha1' => 'H40');

		if (!isset($packs[$algo])) {
			return false;
		}

		$pack = $packs[$algo];

		if (strlen($key) > 64) {
			$key = pack($pack, $algo($key));
		} elseif (strlen($key) < 64) {
			$key = str_pad($key, 64, chr(0));
		}

		$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

		return $algo($opad . pack($pack, $algo($ipad . $data)));
	}
}


