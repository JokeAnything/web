<?php

function getRootPath(){
    return "/home/log";
}

function getFileList($path){
    $list = scandir($path);
    return $list;
}

function isTopPath($path){
    $root = getRootPath();
    if(strcmp($path,$root) == 0){
        return true;
    }else{
        return false;
    }
}

function isDirectory($path){
    return is_dir($path);
}

function isFile($path){
    return is_file($path);
}

function getBaseDir($path){
    return dirname($path);
}
?>