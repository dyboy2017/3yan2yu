<?php
/*
*	chat_list.php
*	消息列表功能
*/

	require_once("index.php");
	session_start();

	//获取消息列表
	if($action == "get_list"){
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足"
			];
			json_echo_exit($response);
		}

		//查询当前用户聊天的对象
		$user_list_sql = "SELECT * FROM simichat_list WHERE l_id='".$_SESSION['id']."'";
		$user_results = api_query($user_list_sql);

		$rows = [];//用于最后的输出结果数组
		while($row = mysqli_fetch_assoc($user_results)){
			$user_heads = mysqli_fetch_assoc(api_query("SELECT * FROM simichat_users WHERE id='".$row['r_id']."'"));
			$row['head_img'] = $user_heads['head_img'];
			$row['nickname'] = $user_heads['nickname'];
			array_push($rows,$row);
		}
		$response = [
			'code'=>1,
			'msg'=>$rows
		];
		json_echo_exit($response);

	}


	//聊天界面消息获取
	if($action == "liaotian_info"){
		//注意信息泄漏问题
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$chat_room = isset($_POST['chat_room_data']) ? intval($_POST['chat_room_data']) : '';
		
		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足"
			];
			json_echo_exit($response);
		}
		//获取消息
		if($chat_room){
			//注意结果顺序
			$chat_sql = "SELECT * FROM simichat_chatroom WHERE chat_room='".$chat_room."' ORDER BY id DESC limit 0,5;" ;
			$rows = [];	//用于存储返回数据
			$results = api_query($chat_sql);
			while($row = mysqli_fetch_assoc($results)){
				array_push($rows,$row);
			}
			$response = [
				'code'=>1,
				'msg'=>$rows
			];
		}
		else{
			$response = [
				'code'=>0,
				'msg'=>"获取消息失败"
			];
		}
		json_echo_exit($response);

	}

	//发送聊天消息
	if ($action == 'send_m') {
		# code...
		//param：msg_data r_id_data chat_room
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$msg = isset($_POST['msg_data']) ? addslashes(trim($_POST['msg_data'])) : '';
		$r_id = isset($_POST['r_id_data']) ? intval($_POST['r_id_data']) : '';
		$chat_room = isset($_POST['chat_room_data']) ? intval($_POST['chat_room_data']) : '';

		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足"
			];
			json_echo_exit($response);
		}

		if($msg && $r_id && $chat_room){

			$time = date('Y-m-d H:i:s');	//生成时间

			/////////////////////////////注意此处的逻辑 状态
			$msg_sql = "INSERT INTO simichat_chatroom (message,type,time,chat_room,r_id,l_id) VALUES ('".$msg."',1,'".$time."','".$chat_room."','".$r_id."','".$_SESSION['id']."')";
			$changemsg_sql = "UPDATE simichat_list SET message='".$msg."',status='已回复' WHERE chat_room='".$chat_room."' AND l_id='".$_SESSION['id']."'";
			$changemsg_sql2 = "UPDATE simichat_list SET message='".$msg."',status='未回复' WHERE chat_room='".$chat_room."' AND l_id='".$r_id."'";
			///////////////////////////////////////

			api_query($msg_sql);
			api_query($changemsg_sql);
			api_query($changemsg_sql2);

			$response = [
				'code'=>1,
				'msg'=>"success"
			];
		}
		else{
			$response = [
				'code'=>0,
				'msg'=>"发送失败，请重试"
			];
		}
		json_echo_exit($response);

	}

	//获取最新的一条消息
	if($action == "newsest_msg"){
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$chat_room = isset($_POST['chat_room_data']) ? intval($_POST['chat_room_data']) : '';

		//验证是否登陆-权限验证
		if($_SESSION['username'] != $username || !$_SESSION['username']){
			$response = [
				'code'=>0,
				'msg'=>"权限不足"
			];
			json_echo_exit($response);
		}

		//获取最新的一条消息
		# 方法：发送消息后需要更新当前聊天窗口的消息
		$newsest_msg_sql = "SELECT * FROM simichat_list WHERE chat_room=";
		//...烂尾了，当时的思路不是很清晰

	}

	//删除会话
	if($action == "del_session"){
		//param : caht_room username 
		$username = isset($_POST['username_data']) ? addslashes(trim($_POST['username_data'])) : '';
		$chat_room = isset($_POST['chat_room_data']) ? intval($_POST['chat_room_data']) : '';

		if($username && $chat_room){
			//验证是否登陆-权限验证
			if($_SESSION['username'] != $username){
				$response = [
					'code'=>0,
					'msg'=>"权限不足"
				];
				json_echo_exit($response);
			}

			//删除会话
			$del_session_sql = "DELETE FROM simichat_list WHERE chat_room='".$chat_room."'";
			api_query($del_session_sql);
			$response = ['code'=>1,'msg'=>'会话及记录已彻底清除!'];
			json_echo_exit($response);

		}
		else{
			$response = ['code'=>0,'msg'=>"权限不足！"];
			json_echo_exit($response);
		}
	}

?>