<?php
    function connectMySql($host,$user,$password){
        $link = mysqli_connect($host, $user, $password, "userinfosys");
        return $link;
    }

    function setDbAccessEncode($dbObj,$encode){
        $character = "set names ${encode}";
        return mysqlExecute($dbObj,$character);
    }

    function closeMySql($obj){
        mysqli_close($obj);
    }

    function mysqlExecute($linkObj,$sql){
        $result = mysqli_query($linkObj,$sql);
        return $result;
    }
?>
