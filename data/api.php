<?php
echo '123';
$tel = trim($_POST['tel']);
$pwd = trim($_POST['pass']);

$conn = @mysqli_connect('localhost','root','root');
if($conn==false){
	echo '网络错误，请联系技术人员';
	exit();
}

mysqli_select_db($conn,'legou');//legou database

mysqli_query($conn,'set names utf8');
$query = mysqli_query($conn,"SELECT * FROM user WHERE tel='$tel'");