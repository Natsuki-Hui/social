<?php 

$conn = @mysqli_connect('localhost','wd1900115','pass123456');
if($conn == false){
    echo json_encode(['error' => 1,'msg' => '网络出小差了，请稍后再试！']);
    exit();
}
mysqli_select_db($conn,'wd1900115');
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

mysqli_close($conn);
?>