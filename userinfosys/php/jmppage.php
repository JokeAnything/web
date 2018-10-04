<?php
function jmpLoginPage($delaySecTime,$jmpPage){
    echo "${delaySecTime} 后自动返回前一个页面";
    echo "<br/>";
    header("refresh:${delaySecTime};url=${jmpPage}");
}
?>