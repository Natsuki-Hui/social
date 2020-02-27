<?php 
	$conn=@mysqli_connect('localhost','wd1900113','pass123456');
	if($conn==false){
		echo json_encode(['error'=>1,'msg'=>'网络错误，请检查网络连接']);
		exit();
	}
	mysqli_select_db($conn,'wd1900113');
	mysqli_query($conn,'set names utf8');

if($_POST['act']=='pub'){

	$desc=$_POST['desc'];
	$pos=$_POST['pos'];
	$tag=$_POST['tag'];
	$time=time();
	$uid = $_POST['uid'];

	// 添加之前处理图片上传
	if(!file_exists('../uploads/')){
		// 创建目录
		mkdir('../uploads/',0777);
	}
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
	mysqli_query($conn,"insert into so_dynamic(descs,pos,tag,addtime,pic,uid) values('$desc','$pos','$tag',$time,'$pics',$uid)");
	echo json_encode(['error'=>0,'msg'=>'发布成功']);

}else if($_POST['act']=='list'){
		// 是请求数据
	$pn = $_POST['pn'];
	$size = $_POST['size'];	// 每页查询多少条数据
	$uid=$_POST['uid']>0?$_POST['uid']:0;
	$start = ($pn-1)*$size;

	$query0=mysqli_query($conn,"select * from so_dynamic where 1 order by addtime desc limit $start,$size");
	while($row0=mysqli_fetch_assoc($query0)){
		$row[]=$row0;
	}

	$query = mysqli_query($conn,"select * from so_dynamic as d left join so_user as u on d.uid=u.uid where 1 order by addtime desc limit $start,$size");
	$list = array();
	while($data=mysqli_fetch_assoc($query)){
		//$data 是每条数据
		$data['addtime'] = format_date($data['addtime']);
		$data['tag'] = explode(',', $data['tag']);	// 转成数组
		$data['pic'] = explode(',', $data['pic']);

		$did=$data['did'];
		$query2=mysqli_query($conn,"select lid from so_likes where uid=$uid and did=$did");

		$zan=0;
		if(mysqli_num_rows($query2)>0){
			$zan=1;
		}
		$data['zan']=$zan;

		// 登录者是否对动态发布者有关注
		$touid = $data['uid'];
		$query3 = mysqli_query($conn,"select * from so_focus where uid=$uid and touid=$touid");
		$gz = 0;
		if(mysqli_num_rows($query3)>0){
			$gz = 1;
		}
		$data['gz'] = $gz;
		$list0[] = $data;	// $list 是三维数组
	}
	$list[]=$list0;
	$list[]=$row;
	echo json_encode(['error'=>0,'msg'=>'success','data'=>$list]);
}else if($_POST['act']=='zan'){
	$uid=$_POST['uid'];
	$did=$_POST['did'];
	$query=mysqli_query($conn,"select * from so_likes where uid=$uid and did=$did");
	if(mysqli_num_rows($query)>0){
		mysqli_query($conn,"delete from so_likes where uid=$uid and did=$did");
	}else{
		mysqli_query($conn,"insert into so_likes(uid,did) values($uid,$did)");
	}

	$query2=mysqli_query($conn,"select count(lid) as c from so_likes where did=$did");
	$cinfo=mysqli_fetch_assoc($query2);
	$num=$cinfo['c'];
	mysqli_query($conn,"update so_dynamic set zannum=$num where did=$did");

	echo json_encode(['error'=>0,'msg'=>'success','data'=>$num]);
	
}else if($_POST['act']=='notice'){
	$uid=$_POST['uid'];
	$touid=$_POST['touid'];
	$query=mysqli_query($conn,"select * from so_focus where uid=$uid and touid=$touid");
	if(mysqli_num_rows($query)>0){
		mysqli_query($conn,"delete from so_focus where uid=$uid and touid=$touid");
	}else{
		mysqli_query($conn,"insert into so_focus(uid,touid) values($uid,$touid)");
	}
	// $query2=mysqli_query($conn,"select count(fid) as c from so_focus where touid=$touid");
	// $cinfo=mysqli_fetch_assoc($query2);
	// $num=$cinfo['c'];
	// mysqli_query($conn,"update so_dynamic set noticenum=$num where touid=$touid");

	echo json_encode(['error'=>0,'msg'=>'success','data'=>$touid]);

	
	
}else if ($_POST['act']=='getd'){
	$uid=$_POST['uid'];
	$list=array();
	$query=mysqli_query($conn,"select count(1) as num from so_dynamic as d where d.uid=$uid");
	$info=mysqli_fetch_assoc($query);

	$query2=mysqli_query($conn,"select count(1) as num2 from so_focus where touid=$uid");
	$info2=mysqli_fetch_assoc($query2);

	$list[]=$info;
	$list[]=$info2;

	echo json_encode(['error'=>0,'msg'=>'修改成功','data'=>$list]);
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