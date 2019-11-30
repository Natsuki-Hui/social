<?php 

$conn = @mysqli_connect('localhost','root','root');
if($conn == false){
    echo json_encode(['error' => 1,'msg' => '网络出小差了，请稍后再试！']);
    exit();
}
mysqli_select_db($conn,'social');
mysqli_query($conn,'set names utf8');
if ($_POST['act']=='reg') {
    $ru = $_POST['ruser'];
    $re = $_POST['remail'];
    $rp = $_POST['rpwd'];
    $rs = $_POST['rsex'];
    $rph = $_POST['rphone'];
    $rd = $_POST['rdesc'];
    $query = mysqli_query($conn,"SELECT uid FROM so_user WHERE email='$re' || phone = $rph");
    if (mysqli_num_rows($query)>0) {
        $info = mysqli_fetch_assoc($query);
        $n = $info['email']==$re?'邮箱':'手机号';
        echo json_encode(['error' => 1,'msg' =>$n.'已被注册']);
        exit();
    }
    mysqli_query($conn,"INSERT INTO so_user(phone,pwd,nickname,email,sex,descs) VALUES('$rph','$rp','$ru','$re','$rs','$rd')");
    echo json_encode(['error' => 0,'msg' =>'注册成功']);  
}

mysqli_close($conn);
?>