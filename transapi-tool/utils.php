<?php
function trimString($srcStr,$trimedObj){
    if($srcStr===""){
        return "";
    }
    return str_ireplace($trimedObj,'',$srcStr);
}
?>