<?php
// 设置编码格式为utf-8
header("Content-Type: text/html;charset=utf-8");
include_once "mysql.php";
include_once "jmppage.php";

// 提取请求参数
$parameter = $_SERVER['QUERY_STRING'];
$req = convertParamToKV($parameter);

if(strlen($req['username']) == 0){
        echo "未指定需要注销的用户";
        echo "<br/>";
        jmpLoginPage(30,"../login.html");
        die();
}

$user = $req['username'];

$db = connectMySql("localhost:3306","root","root");
if(!$db){
    echo "数据库连接失败";
    echo "<br/>";
    jmpLoginPage(3,"../login.html");
    die();
}

setDbAccessEncode($db,"utf8");

$sql_statment = "delete from user where username=\"${user}\"";
$res = mysqlExecute($db,$sql_statment);
if(!$res){
    echo "数据库访问失败，注销失败";
}else{
    echo "注销成功";
}

closeMySql($db);
echo "<br/>";
jmpLoginPage(3,"../login.html");

function convertParamToKV($param){
    //解析a=b&c=d&e=f到关联数组.
    parse_str($param,$kv);
    return $kv;
}
?>