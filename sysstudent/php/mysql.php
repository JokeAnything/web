<?php
    function connectMySql($host,$user,$password){
        $link = mysqli_connect("localhost:3306", "root", "root", "sysstudent");
        if (!$link) {
            echo "error code:".mysqli_connect_errno()."<br/>";
            echo "error msg:".mysqli_connect_error()."<br/>";
            exit;
        }
        echo "connected successfully"."<br/>";
        return $link;
    }

    function closeMySql($obj){
        mysqli_close($obj);
    }

    function mysqlExecute($linkObj,$sql){
        $result = mysqli_query($linkObj,$sql);
        if (!$result) {
            echo "error code:".mysqli_connect_errno()."<br/>";
            echo "error msg:".mysqli_connect_error()."<br/>";
            exit;
        }
        return $result;
    }
?>
