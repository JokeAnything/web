<?php

function get_data_by_acibaapi($query){
    $w=$query;
    $key='643BA6704D25A33A378C89A9DF405ED4';
    $type='json';
    $url="http://dict-co.iciba.com/api/dictionary.php?w=$w&key=$key&type=$type";
    $content = file_get_contents($url);
    echo $content;
    $res = json_decode($content,true);
    var_export($res);
    return $ret;
}

$result = get_data_by_acibaapi('played');


?>