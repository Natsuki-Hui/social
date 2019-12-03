<?php 

$conn = @mysqli_connect('localhost','wd1900115','pass123456');
if($conn == false){
    echo json_encode(['error' => 1,'msg' => '网络出小差了，请稍后再试！']);
    exit();
}
mysqli_select_db($conn,'wd1900115');
mysqli_query($conn,'set names utf8');
if($_POST['act']=='pub'){
	$info = $_POST["info"];
	$pos = $_POST["pos"];
	$tag = rtrim($_POST['tag'],',');
	$time = time();
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
	mysqli_query($conn,"INSERT INTO so_dynamic(descs,pos,tag,addtime,pic) VALUES('$info','$pos','$tag','$time','$pics')");
	echo json_encode(['error'=>0,'msg'=>'发布成功']);
}else if($_POST['act']=='list'){
	// 是请求数据
	$pn = $_POST['pn'];
	$size = $_POST['size'];	// 每页查询多少条数据
	$start = ($pn-1)*$size;
	$query = mysqli_query($conn,"select * from so_dynamic where 1 order by addtime desc limit $start,$size");
	$list = array();
	while($data=mysqli_fetch_assoc($query)){
		//$data 是每条数据
		$data['addtime'] = format_date($data['addtime']);
		$data['tag'] = explode(',', $data['tag']);	// 转成数组
		$data['pic'] = explode(',', $data['pic']);

		$list[] = $data;	// $list 是三维数组
	}
	echo json_encode(['error'=>0,'msg'=>'success','data'=>$list]);
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