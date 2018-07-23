<?php 
	/**
	*   author：DYBOY
	*	time：2018-02-04
	*	description：上传图片返回图片外链api
	*/

	require_once('index.php');
	session_start();

	function get_rand_str( $length = 16 ) {
	    $str = substr(md5(time()), 0, $length);
	    return $str;
	}

	if($action == "up_img"){
		if($_FILES["file"]["size"] <= 1048576){ 
			if($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/gif"){
				if($_FILES["file"]["error"] > 0) {	//判断是否有错误
					$response = ['code'=>0,'msg'=>$_FILES["file"]["error"]];
					echo json_encode($response);
					exit();
				}
				else{
					$rand_name = get_rand_str();
					if($_FILES["file"]["type"] == "image/png"){
						$rand_name = $rand_name.".png";
					}
					elseif($_FILES["file"]["type"] == "image/jpeg"){
						$rand_name = $rand_name.".jpg";
					}
					else{
						$rand_name = $rand_name.".gif";
					}
					move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" .$rand_name);
				  	//处理图片成功
				 	$response = ['code'=>1,'url'=>DY_ROOT."function/upload/".$rand_name];
				 	echo json_encode($response);
				 	exit();
				}
			}
			else{
				//文件类型错误
			  	$response = ['code'=>0,'msg'=>"文件类型(png,jpg,gif)错误！"];
			  	echo json_encode($_FILES);
			  	exit();
			}
		}
		else{
			$response = ['code'=>0,'msg'=>"仅允许上传<1M大小图片！"];
		  	echo json_encode($response);
		  	exit();
		}
	}
	
	
 ?>