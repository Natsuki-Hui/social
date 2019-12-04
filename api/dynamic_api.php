<?php 

$conn = @mysqli_connect('localhost','wd1900115','pass123456');
// $conn = @mysqli_connect('localhost','root','root');
if($conn == false){
    echo json_encode(['error' => 1,'msg' => '网络出小差了，请稍后再试！']);
    exit();
}
mysqli_select_db($conn,'wd1900115');
// mysqli_select_db($conn,'social');
mysqli_query($conn,'set names utf8');
if($_POST['act']=='pub'){
	$info = $_POST["info"];
	$pos = $_POST["pos"];
	$uid = $_POST["uid"];
	$tag = rtrim($_POST['tag'],',');
	$time = time();
	if(!file_exists('../uploads/')){
		// 创建目录
		mkdir('../uploads/',0777);
	}
	// echo $uid;
	$parr = array();
	for($i=1; $i<=3; $i++){
		if(isset($_POST['pic'.$i])){		// isset(变量$_POST['pic2']) 函数，判断变量是否存在
			// 上传图片
			$pic = $_POST['pic'.$i];
			if(strstr($pic,',')){
				$arr = explode(',', $pic);
				$pic = $arr[1];		//判断是否有逗号 如果有就截取后半部分
			}
			$name = date('YmdHis').mt_rand(10000,99999).'.png';
			file_put_contents('../uploads/'.$name, base64_decode($pic));
			$parr[] = $name;
		}
	}

	$pics = implode(',', $parr);
	mysqli_query($conn,"INSERT INTO so_dynamic(descs,pos,tag,addtime,pic,uid) VALUES('$info','$pos','$tag','$time','$pics',$uid)");
	echo json_encode(['error'=>0,'msg'=>'发布成功']);
}else if($_POST['act']=='list'){
	// 是请求数据
	$pn = $_POST['pn'];
	$uid = $_POST['uid']>0?$_POST['uid']:0;
	$size = $_POST['size'];	// 每页查询多少条数据
	$start = ($pn-1)*$size;
	$query = mysqli_query($conn,"select * from so_dynamic where 1 order by addtime desc limit $start,$size");
	$list = array();
	// $query3 = mysqli_query($conn,"select fid from so_focus where uid = $uid");
	// echo "select fid from so_focus where uid = $uid";
	while($data=mysqli_fetch_assoc($query)){
		//$data 是每条数据
		$data['addtime'] = format_date($data['addtime']);
		$data['tag'] = explode(',', $data['tag']);	// 转成数组
		$data['pic'] = explode(',', $data['pic']);

		// 查询是否有状态
		$did = $data['did'];
		$query2 = mysqli_query($conn,"select lid from so_likes where uid = $uid and did = $did");
		$zan = 0;
		if(mysqli_num_rows($query2)>0){
			$zan = 1;
		}
		$data['zan'] = $zan;
		

		$touid = $data['touid'];
		$query4 = mysqli_query($conn,"select fid from so_focus where uid = $uid and touid = $touid");
		$follow = 0;
		if(mysqli_num_rows($query4)>0){
			$follow = 1;
		}
		$data['touid'] = $follow;

		$list[] = $data;	// $list 是三维数组
	}
	echo json_encode(['error'=>0,'msg'=>'success','data'=>$list]);
}else if($_POST['act']=='zan'){
	$uid = $_POST['uid'];
	$did = $_POST['did'];

	$query = mysqli_query($conn,"SELECT * FROM so_likes WHERE uid=$uid and did = $did");
	if(mysqli_num_rows($query)>0) {
		mysqli_query($conn,"DELETE FROM so_likes WHERE uid=$uid and did = $did");
	}else{
		mysqli_query($conn,"INSERT INTO so_likes(uid,did) VALUES($uid,$did)");
	}

	$query2 = mysqli_query($conn,"select count(lid) as c from so_likes where did=$did");
	$cinfo = mysqli_fetch_assoc($query2);
	$num = $cinfo['c'];

	mysqli_query($conn,"update so_dynamic set zannum=$num where did=$did");
	echo json_encode(['error'=>0,'msg'=>'success','data' => $num]);
}else if($_POST['act']=='follow'){
    $uid = $_POST['uid'];
    $touid = $_POST['touid'];

    $query = mysqli_query($conn,"SELECT * FROM so_focus WHERE uid=$uid and touid = $touid");
    if(mysqli_num_rows($query)>0) {
        mysqli_query($conn,"DELETE FROM so_focus WHERE uid=$uid and touid = $touid");
    }else{
        mysqli_query($conn,"INSERT INTO so_focus(uid,touid) VALUES($uid,$touid)");
    }

    $query2 = mysqli_query($conn,"select count(fid) as f from so_focus where touid=$touid");
    $cinfo = mysqli_fetch_assoc($query2);
    $num = $cinfo['f'];
    echo json_encode(['error'=>0,'msg'=>'success','data' => $num]);
}
mysqli_close($conn);

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
?>