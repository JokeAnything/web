<?php
    function connectMySql($host,$user,$password){
        $link = mysql_connect($host, $user, $password);
        var_dump($link);
        if (!$link) {
            echo 'error code:'.mysql_errorno()."\n";
            echo 'error msg:'.mysql_error()."\n";
            exit;
        }
        echo 'connected successfully';
        return $link;
    }

    function closeMySql($obj){
        mysql_close($obj);
    }

    function mysqlExecute($sql){
        $result = mysql_query($sql);
        if (!$result) {
            echo 'error code:'.mysql_errorno()."\n";
            echo 'error msg:'.mysql_error()."\n";
            exit;
        }
        return $result;
    }
?>