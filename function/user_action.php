<?php
/*
*	user_action.php
*	用户登陆、注册、修改资料、清除聊天记录等行为
*/
	require_once("index.php");
	session_start();

	//用户登陆
	if($action == "user_login"){
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$password = isset($_POST['password_data']) ? addslashes(trim($_POST['password_data'])) : '';

		if($username && $password){
			if($_SESSION[$username]!=''){
				$response=[
					'code'=>0,
					'msg'=>'该用户已登录'
				];
				json_echo_exit($response);
			}
			$password = md5($password);
			$login_sql = "SELECT * FROM simichat_users WHERE username ='".$username."' AND password = '".$password."'";
			$results = api_query($login_sql);
			if(!mysqli_num_rows($results)){
				$response = [
					'code'=>0,
					'msg'=>'账号或密码错误'
				];
			}
			else{
				$response = [
					'code'=>1,
					'url'=>''//跳转地址
				];
				$row = mysqli_fetch_assoc($results);
				$_SESSION['id'] = $row['id'];
				$_SESSION['username'] = $username;

			}
			json_echo_exit($response);

		}
		else{
			$response = [
				'code'=>'0',
				'msg'=>'请输入完整信息！'
			];
			json_echo_exit($response);
		}
	}

	//用户获取验证码
	if($action == 'get_code'){
		$rand_str = getRandNum(6,false);
		$_SESSION['rand_code'] = $rand_str;
		$response = ['code'=>1,'msg'=>$rand_str];
		json_echo_exit($response);
	}

	//用户注册
	if($action == "user_reg"){
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$password = isset($_POST['password_data']) ? addslashes(trim($_POST['password_data'])) : '';
		$rand_code = isset($_POST['rand_code_data']) ? intval($_POST['rand_code_data']) : '';
		///.....

		if($username && $password && $rand_code){
			
			//数据规范化检查
			if(strlen($username)<5 || strlen($username)>8 ){
				$response = ['code'=>0,'msg'=>'用户名长度为5~8位'];
				json_echo_exit($response);
			}
			if(!preg_match("/[A-Za-z]/",$username)){
				$response = ['code'=>0,'msg'=>'用户名只能为字母组合'];
				json_echo_exit($response);
			}
			if(strlen($password)<5 || strlen($password)>16 ){
				$response = ['code'=>0,'msg'=>'密码长度为5~16位'];
				json_echo_exit($response);
			}
			if($rand_code != $_SESSION['rand_code']){
				$response = ['code'=>0,'msg'=>'邀请码错误！'];
				json_echo_exit($response);
			}
			
			//检查用户是否重复
			$check_sql = "SELECT * FROM simichat_users WHERE username='".$username."'";
			if(mysqli_num_rows(api_query($check_sql))){
				$response = ['code'=>0,'msg'=>'当前用户名已被注册！'];
				json_echo_exit($response);
			}

			//插入用户
			$password2 = md5($password);
			$reg_sql = "INSERT INTO simichat_users (username,password,back_mima) VALUES ('".$username."','".$password2."','".$password."')";
			api_query($reg_sql);
			$response = ['code'=>1,'msg'=>"用户".$username."注册成功,请牢记您的密码！"];
			json_echo_exit($response);
		}
		else{
			$response = [
				'code'=>0,
				'msg'=>'注册信息不完整！'.$username.$password.$rand_code
			];
			json_echo_exit($response);
		}
		
	}

	//用户个人信息
	if($action == "get_user_info"){
		$username = isset($_GET['username_data']) ? addslashes(trim($_GET['username_data'])) : '';
		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足".$username
			];
			json_echo_exit($response);
		}

		$userinfo_sql = "SELECT * FROM simichat_users WHERE id='".$_SESSION['id']."'";
		$results = mysqli_fetch_assoc(api_query($userinfo_sql));
		$response = [
			'code'=>1,
			'msg'=>$results
		];
		json_echo_exit($response);

	}

	//用户修改资料
	if($action == "update_data"){
			$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
			$nickname = isset($_POST['nickname_data']) ? addslashes(trim($_POST['nickname_data'])) : '';
			$sex = isset($_POST['sex_data'])?addslashes(trim($_POST['sex_data'])):'';
			$phone = isset($_POST['phone_data'])?addslashes(trim($_POST['phone_data'])):'';
			$kouling = isset($_POST['kouling_data'])?addslashes(trim($_POST['kouling_data'])):'';
			
			if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
					'code'=>0,
					'msg'=>"权限不足"
				];
				json_echo_exit($response);
			}

			if($nickname && $kouling){
				$updata_sql = "UPDATE simichat_users SET nickname='".$nickname."',sec_token='".$kouling."',sex='".$sex."',phone='".$phone."' WHERE username='".$username."'";
				api_query($updata_sql);
				$response = [
					'code'=>1,
					'msg'=>'资料已更新'
				];
				json_echo_exit($response);
			}
			else{
				$response = [
					'code'=>'0',
					'msg'=>'请输入完整信息！'
				];
				json_echo_exit($response);
			}
	}



	//用户更新头像链接到数据库
	if($action == "update_head"){
		//...
		$img_src = isset($_POST['img_src_data']) ? addslashes(trim($_POST['img_src_data'])) : '';
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足".$username
			];
			json_echo_exit($response);
		}
		$uphead_sql = "UPDATE simichat_users SET head_img='".$img_src."' WHERE id='".$_SESSION['id']."'";
		api_query($uphead_sql);
	}


	//用户清除聊天记录
	if($action == "clear_lists"){
		//...
		//此功能放置到chat_list.php中
	}


	//用户搜索好友
	if($action == "search_users"){
		$keyword = isset($_GET['keyword_data']) ? addslashes(trim($_GET['keyword_data'])) : '';
		if($keyword){
			$search_sql = "SELECT * FROM simichat_users WHERE nickname LIKE '%".$keyword."%' OR username LIKE '%".$keyword."%'";
			$results = api_query($search_sql);
			$rows = []; //存储数据的rows
			while($row = mysqli_fetch_assoc($results)){
				array_push($rows,$row);
			}
			if($rows[0] == ""){
				$response = [
					'code'=>0,
					'msg'=>"暂无搜索结果!"
				];
				json_echo_exit($response);
			}
			$response = [
				'code'=>1,
				'msg'=>$rows
			];
			json_echo_exit($response);
		}
		else{
			$response = [
				'code'=>'0',
				'msg'=>'请输入昵称、用户名搜索！'
			];
			json_echo_exit($response);
		}
	}


	//申请好友时 验证口令
	if($action == "yanzheng_token"){
		$token = isset($_POST['submit_token']) ? addslashes(trim($_POST['submit_token'])) : '';
		$ruserid = isset($_POST['r_user_id']) ? addslashes(trim($_POST['r_user_id'])) : '';
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足"
			];
			json_echo_exit($response);
		}

		if($token && $ruserid){
			if($ruserid == $_SESSION['id']){
				$response = [
					'code'=>0,
					'msg'=>"不能与自己建立会话关系！"
				];
				json_echo_exit($response);
			}
			$yanzheng_sql = "SELECT id,sec_token FROM simichat_users WHERE id='".$ruserid."' AND sec_token='".$token."'";
			$safe_sql = "SELECT * FROM simichat_list WHERE r_id='".$ruserid."' AND l_id='".$_SESSION['id']."'";
			if(mysqli_num_rows(api_query($yanzheng_sql))){
				//已建立会话验证
				if(mysqli_num_rows(api_query($safe_sql))){
					$response = [
						'code'=>0,
						'msg'=>"已经建立会话，无需重复添加"
					];
					json_echo_exit($response);
				}
				//....
				//此处还需要建立会话的操作
				$time = date('Y-m-d H:i:s');
				$chat_room = time();

				//邀请用户
				$build_sql1 = "INSERT INTO simichat_list (l_id,r_id,message,time,ms_type,status,chat_room) VALUES ('".$ruserid."','".$_SESSION['id']."','你已经通过了我设置聊天口令，现在开始来聊天吧！  --本条消息由系统发送','".$time."',1,'未回复','".$chat_room."')";
				//当前用户
				$build_sql2 = "INSERT INTO simichat_list (l_id,r_id,message,time,ms_type,status,chat_room) VALUES ('".$_SESSION['id']."','".$ruserid."','你好  --本条消息由系统发送','".$time."',1,'已回复','".$chat_room."')";

				$build_sql3 = "INSERT INTO simichat_chatroom (message,type,time,chat_room,l_id,r_id) VALUES ('你已经通过了我设置聊天口令，现在开始来聊天吧！   --本条消息由系统发送',1,'".$time."','".$chat_room."','".$ruserid."','".$_SESSION['id']."')";

				$build_sql4 = "INSERT INTO simichat_chatroom (message,type,time,chat_room,l_id,r_id) VALUES ('你好   --本条消息由系统发送',1,'".$time."','".$chat_room."','".$_SESSION['id']."','".$ruserid."')";
				
				api_query($build_sql1);
				api_query($build_sql2);
				api_query($build_sql3);
				api_query($build_sql4);

				//写入聊天记录
				//....
				$response = [
					'code'=>1,
					'msg'=>"已经与对方建立聊天会话"
				];
				json_echo_exit($response);
			}
			else{
				$response = [
					'code'=>0,
					'msg'=>"口令错误，请重新尝试"
				];
				json_echo_exit($response);
			}
		}
		else{
			$response = [
				'code'=>0,
				'msg'=>'请填写对方聊天口令'
			];
			json_echo_exit($response);
		}
	}


	//检查更新
	if($action == "update_app"){
		$update_sql = "SELECT * FROM simichat_update ORDER BY id DESC LIMIT 1";
		$results = mysqli_fetch_assoc(api_query($update_sql));
		$response = [
			'code'=>1,
			'msg'=>$results
		];
		json_echo_exit($response);
	}


	//用户注销
	if($action == "user_logout"){
		unset($_SESSION['username']);
		unset($_SESSION['id']);
	}
?>