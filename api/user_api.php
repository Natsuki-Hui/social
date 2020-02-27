<?php
session_start();
$conn=@mysqli_connect('localhost','wd1900113','pass123456');
if($conn==false){
    echo json_encode(['error'=>1,'msg'=>'网络出错，请检查网络连接']);
    exit();
}
mysqli_select_db($conn,'wd1900113');
mysqli_query($conn,'set names utf8');

if($_POST['act']=='reg'){

	$ru=$_POST['ruser'];
	$re=$_POST['remail'];
	$rp=$_POST['rpwd'];
	$rs=$_POST['rsex'];
	$rph=$_POST['rphone'];
	$rd=$_POST['rdesc'];

	$query=mysqli_query($conn,"select * from so_user where email='$re' || phone='$rph'");
	if(mysqli_num_rows($query)>0){
	    $info=mysqli_fetch_assoc($query);
	    $n=$info['email']==$re?'邮箱':'手机号';
	    echo json_encode(['error'=>1,'msg'=>$n.'已被注册']);
	    exit();
	}

	mysqli_query($conn,"insert into so_user(phone,pwd,nickname,email,sex,descs) values('$rph','$rp','$ru','$re','$rs','$rd')");
	echo json_encode(['error'=>0,'msg'=>'注册成功']);

}else if ($_POST['act']=='login'){

	$phone=$_POST['phone'];
	$pwd=$_POST['pwd'];
	$verify=$_POST['verify'];

	$query=mysqli_query($conn,"SELECT * FROM `so_user` WHERE phone='$phone' and pwd='$pwd'");
	if(mysqli_num_rows($query)<=0){
		echo json_encode(['error'=>1,'msg'=>'手机号或密码错误']);
		exit();
	}

	if(strtolower($_SESSION["verifyimg"]) != strtolower($verify)){
		echo json_encode(['error'=>1,'msg'=>'验证码错误']);
		exit();
	}
	$_SESSION["verifyimg"] = "";

	// 查到了
	$info = mysqli_fetch_assoc($query); // 用户在数据库的记录

	echo json_encode(['error'=>0,'msg'=>'登录成功','data'=>$info]);

}else if ($_POST['act']=='update'){
	$uid=$_POST['uid'];
	$pic=$_POST['pic'];
	$myname=$_POST['myname'];
	$mysex=$_POST['mysex'];
	$myphone=$_POST['myphone'];
	$myemail=$_POST['myemail'];
	$mydesc=$_POST['mydesc'];

	if(isset($_POST['pic'])){
		if(strstr($pic,',')){
			$arr = explode(',', $pic);
			$pic = $arr[1];		//判断是否有逗号 如果有就截取后半部分
		}
		$name = date('YmdHis').mt_rand(10000,99999).'.png';
		file_put_contents('../uploads/'.$name, base64_decode($pic));
	}


	$query=mysqli_query($conn,"UPDATE `so_user` SET `phone`='$myphone',`photo`='$name',`nickname`='$myname',`email`='$myemail',`sex`='$mysex',`descs`='$mydesc' WHERE uid={$uid}");
	// if(mysqli_num_rows($query)<=0){
	// 	echo json_encode(['error'=>1,'msg'=>'请检查网络连接']);
	// 	exit();
	// }
	$query2=mysqli_query($conn,"select * from `so_user` where uid={$uid}");
	$info=mysqli_fetch_assoc($query2);
	echo json_encode(['error'=>0,'msg'=>'修改成功','data'=>$info]);
}else{
	//获取个人资料
	$uid=$_POST['uid'];
	$query=mysqli_query($conn,"select * from `so_user` where uid={$uid}");
	$info=mysqli_fetch_assoc($query);
	echo json_encode(['error'=>0,'msg'=>'修改成功','data'=>$info]);
}

function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}
mysqli_close($conn);