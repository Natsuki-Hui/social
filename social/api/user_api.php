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
if ($_POST['act']=='reg') {
    $ru = $_POST['ruser'];
    $re = $_POST['remail'];
    $rp = $_POST['rpwd'];
    $rs = $_POST['rsex'];
    $rph = $_POST['rphone'];
    $rd = $_POST['rdesc'];
    $query = mysqli_query($conn,"SELECT * FROM so_user WHERE email='$re' || phone = $rph");
    if (mysqli_num_rows($query)>0) {
        $info = mysqli_fetch_assoc($query);
        $n = $info['email']==$re?'邮箱':'手机号';
        echo json_encode(['error' => 1,'msg' =>$n.'已被注册','info'=>$info]);
        exit();
    }
    mysqli_query($conn,"INSERT INTO so_user(phone,pwd,nickname,email,sex,descs) VALUES('$rph','$rp','$ru','$re','$rs','$rd')");
    echo json_encode(['error' => 0,'msg' =>'注册成功','info'=> mysqli_fetch_assoc($query)]);  
}
if ($_POST['act']=='login'){
    $lp = $_POST['lpwd'];
    $lph = $_POST['lphone'];
    $query = mysqli_query($conn,"SELECT * FROM so_user WHERE phone = $lph");
    $info = mysqli_fetch_assoc($query);
    if ((!mysqli_num_rows($query)>0) || $info['pwd']!=$lp) {       
        $n = $info['phone']==$lph?'密码':'手机号';   
        echo json_encode(['error' => 1,'msg' =>$n.'不正确']);
        exit();
    }
    echo json_encode(['error' => 0,'msg' =>'登录成功','data' => $info]);
}
if($_POST['act']=='modi') {  
    $uid = $_POST['uid'];
    $Im = $_POST['img-modify'];
    $Nm = $_POST['name-modify'];
    $Sm = $_POST['sex-modify'];
    $Em = $_POST['email-modify'];
    $Tm = $_POST['txt-modify'];
    mysqli_query($conn,"UPDATE so_user SET photo = '$Im', nickname= '$Nm',email= '$Em' ,sex= '$Sm' ,descs= '$Tm' WHERE uid = '$uid' ");
    $query = mysqli_query($conn,"SELECT * FROM so_user WHERE uid= $uid");
    $info = mysqli_fetch_assoc($query);
    echo json_encode(['error' => 0,'msg' =>'修改成功','data' => $info]);
}
if($_POST['act']=='follow'){
    $uid = $_POST['uid'];
    $touid = $_POST['touid'];
    $query = mysqli_query($conn,"SELECT * FROM so_focus WHERE uid=$uid and touid = $touid");
    if(mysqli_num_rows($query)>0) {
        mysqli_query($conn,"DELETE FROM so_focus WHERE uid=$uid and touid = $touid");
    }else{
        mysqli_query($conn,"INSERT INTO so_focus(uid,touid) VALUES($uid,$touid)");
    }
    // $query2 = mysqli_query($conn,"select count(fid) as f from so_focus where touid=$touid");
    // $cinfo = mysqli_fetch_assoc($query2);
    // $num = $cinfo['f'];
    // echo json_encode(['error'=>0,'msg'=>'success','data' => $num]);
    // $query = mysqli_query($conn,"select * from so_focus where uid=$uid and touid=$touid");
    // if(mysqli_num_rows($query)>0){
    //     // 有记录，是取消关注
    //     mysqli_query($conn,"delete from so_focus where uid=$uid and touid=$touid");
    // }else{
    //     // 没有记录，要关注
    //     mysqli_query($conn,"insert into so_focus(uid,touid) values($uid,$touid)");
    // }
    echo json_encode(['error'=>0,'msg'=>'关注成功']);
}
mysqli_close($conn);
?>