<?php 

$conn = @mysqli_connect('localhost','root','root');
if($conn == false){
    echo json_encode(['error' => 1,'msg' => '网络出小差了，请稍后再试！']);
    exit();
}
mysqli_select_db($conn,'social');
mysqli_query($conn,'set names utf8');
$info = $_POST["info"];
$pos = $_POST["pos"];
$tag = rtrim($_POST['tag'],',');
$time = time();
mysqli_query($conn,"INSERT INTO so_dynamic(descs,pos,tag,addtime) VALUES('$info','$pos','$tag','$time')");
echo json_encode(['error'=>0,'msg'=>'发布成功']);
mysqli_close($conn);
?>