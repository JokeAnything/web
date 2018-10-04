<?php
// 设置编码格式为utf-8
header("Content-Type: text/html;charset=utf-8");
include_once "mysql.php";
include_once "jmppage.php";

if(!isset($_POST["user"]) ||
   !isset($_POST["password"]) ||
   !isset($_POST["pass_comfirm"])){
        echo "输入的用户名或密码无效";
        echo "<br/>";
        jmpLoginPage(3,"../register.html");
        die();
}

$user = $_POST["user"];
$password = $_POST["password"];
$comfrim_passwd = $_POST["pass_comfirm"];

if(strcmp($password,$comfrim_passwd) != 0){
    echo "两次输入的密码不一致";
    echo "<br/>";
    jmpLoginPage(3,"../register.html");
    die();
}

$target = $password."2E2E5D500D5CF14612541E76D32FD5E7";
$passwdmd5 = md5($target);
$birthdate = $_POST["birth_date"];
$homeaddr = $_POST["home_addr"];

$db = connectMySql("localhost:3306","root","root");
if(!$db){
    echo "数据库连接失败";
    echo "<br/>";
    jmpLoginPage(3,"../register.html");
    die();
}

setDbAccessEncode($db,"utf8");

$sql_statment = "select username from user where username=\"${user}\"";
$res = mysqlExecute($db,$sql_statment);
if(!res){
    closeMySql($db);
    echo "数据库访问失败";
    echo "<br/>";
    jmpLoginPage(3,"../register.html");
    die();
}

$row = mysqli_fetch_array($res,MYSQLI_ASSOC);

if($row){
    closeMySql($db);
    echo "用户名：".$row['username']."<br/>";
    echo "状态："."已注册"."<br/>";
    jmpLoginPage(3,"../register.html");    
    die();
}

$sql_statment="insert into user(username,password,birthdate,homeaddr) values(\"${user}\",\"${passwdmd5}\",\"${birthdate}\",\"${homeaddr}\")";

$res = mysqlExecute($db,$sql_statment);

if(!$res){
    echo "注册失败";
    echo "<br/>";
    jmpLoginPage(3,"../register.html");
}else{
    echo "注册成功";
    echo "<br/>";
    jmpLoginPage(3,"../login.html");
}
?>