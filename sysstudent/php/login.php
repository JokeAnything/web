<?php
    include_once "mysql.php";
    $link = connectMySql('localhost:3306','root','root');

    // 设置缺省的数据库
    $useDb = 'use sysstudent';
    mysqlExecute($link,$useDb);

    // 查询数据
    $sql = 'select student.*,class.name from student inner join class on student.c_id = class.c_id';

    $res = mysqlExecute($link,$sql);

    // 向php数组中追加数据
    while($result = mysqli_fetch_row($res)){
        $arrResult [] = $result;
    }

    for($i = 0; $i < count($arrResult); $i++){
        for($j=0;$j<count($arrResult[$i]);$j++){
            echo "{$arrResult[$i][$j]},";
        }
        echo "<br>";
    }
    closeMySql($link);
?>
