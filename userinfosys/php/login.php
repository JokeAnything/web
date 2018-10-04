<?php

// 设置编码格式为utf-8
header("Content-Type: text/html;charset=utf-8");
include_once "mysql.php";
include_once "jmppage.php";

if(!isset($_POST["user"]) ||
   !isset($_POST["password"])){
        echo "用户名或密码错误";
        echo "<br/>";
        jmpLoginPage(3,"../login.html");
        die();
}

$user = $_POST["user"];
$password = $_POST["password"];
$target = $password."2E2E5D500D5CF14612541E76D32FD5E7";
$passwdmd5 = md5($target);

$db = connectMySql("localhost:3306","root","root");
if(!$db){
    echo "登录数据库失败";
    echo "<br/>";
    jmpLoginPage(3,"../login.html");
    die();
}
setDbAccessEncode($db,"utf8");

$sql_statment="select * from user where username=\"${user}\" and password=\"${passwdmd5}\"";

$res = mysqlExecute($db,$sql_statment);
if(!res){
    closeMySql($db);
    echo "数据库访问失败";
    echo "<br/>";
    jmpLoginPage(3,"../login.html");
    die();
}

// 显示信息
$row = mysqli_fetch_array($res,MYSQLI_ASSOC);
if(!$row){
    closeMySql($db);
    echo "用户名或密码错误";
    echo "<br/>";
    jmpLoginPage(3,"../login.html");
    die();
}
// 显示信息
echo "<h1 align=\"center\">个人信息</h1>";
echo "<hr align=\"center\" color=\"#FF0000\" size=\"5\" width=\"90%\"/>";
echo "<h1 align=\"center\"></h1>";
echo "<table border=\"1\" align=\"center\">";

//mysqli_query
while($row)
{
  echo "<tr>";
  echo "<td>"."用户名"."</td>";
  echo "<td>".$row['username']."</td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>"."出生日期:"."</td>";
  echo "<td>".$row['birthdate']."</td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>"."家庭住址:"."</td>";
  echo "<td>".$row['homeaddr']."</td>";
  echo "</tr>";

  echo "<tr>";
  echo "<td>"."注册时间:"."</td>";
  echo "<td>".$row['ctime']."</td>";
  echo "</tr>";

  echo "<tr align=\"center\">";
  echo "<td colspan=\"2\">"."<a href=\"./unregister.php?username=".$row['username']."\">"."注销用户"."</a>"."</td>";
  echo "</tr>";
  $row = mysqli_fetch_array($res,MYSQLI_ASSOC);
}
echo "</table>";

closeMySql($db);
?>