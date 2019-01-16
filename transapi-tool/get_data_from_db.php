<?php

// src-api youdao
const SRC_API_YOUDAO = 0;

// src-api kingsoft
const SRC_API_KINGSOFT = 1;


function connectMySql($host,$user,$password){
    $link = mysqli_connect($host, $user, $password, "db_words_api_cache");
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
    return mysqli_query($linkObj,$sql);
}

function initDataBase(){
    $dbObj=connectMySql('localhost','root','root');
    if(!$dbObj){
        setLastError(ERR_DB_CONNECT_FAIL);
        return false;
    }
    $ret = setDbAccessEncode($dbObj,'utf8');
    if(!$ret){
        setLastError(ERR_DB_SETCHARSET_FAIL);
        closeMySql($dbObj);
        return false;
    }else{
        setLastError(ERR_CUSTOM_SUCCESS);
        return $dbObj;
    }
}

function unInitDataBase($dbObj){
    closeMySql($dbObj);
}


function getKeywordInfoFromDB($dbObj,$query,&$src)
{
    $kingsoft = searchKeywordFromDB($dbObj,'tb_words_kingsoft',$query);
    if(!$kingsoft)
    {
        $src = SRC_API_YOUDAO;
        return searchKeywordFromDB($dbObj,'tb_words_youdao',$query);
    }else{
        $src = SRC_API_KINGSOFT;
        return $kingsoft;
    }
}

function searchKeywordFromDB($dbObj,$tableName,$query)
{
    $query_sql='select * from '.$tableName.' where query="'.$query.'"';
    $execResult = mysqlExecute($dbObj,$query_sql);
    if($execResult){
        // fetch data.
        $fetchRes = mysqli_fetch_assoc($execResult);
        if(!$fetchRes){
            setLastError(ERR_DB_ACCESS_FAIL);
            return false;
        }else{
            setLastError(ERR_CUSTOM_SUCCESS);
            return $fetchRes;
        }
    }else{
        setLastError(ERR_DB_ACCESS_FAIL);
        return false;
    }
}

function insertKeywordInfoToDB($dbObj,$jsonArray,$src){
    if($src === SRC_API_KINGSOFT){
        return insertKeywordInfoToKingsoft($dbObj,$jsonArray);
    }else if($src === SRC_API_YOUDAO){
        return insertKeywordInfoToYoudao($dbObj,$jsonArray);
    }else{
        return false;
    }
}

function insertKeywordInfoToYoudao($dbObj,$jsonArray){

    // try to parse data and into database.
    if(!isset($jsonArray['query'])){
        setLastError(ERR_DB_KEYWORD_INVALID);
        return false;
    }
    $query = base64_encode($jsonArray['query']);
    $us_phonetic="";
    $phonetic="";
    $uk_phonetic="";
    $wfs="";
    $explains="";
    if(isset($jsonArray['basic'])){
        if(isset($jsonArray['basic']['us-phonetic'])){
            $us_phonetic = base64_encode($jsonArray['basic']['us-phonetic']);
        }
        if(isset($jsonArray['basic']['phonetic'])){
            $phonetic = base64_encode($jsonArray['basic']['phonetic']);
        }
        if(isset($jsonArray['basic']['uk-phonetic'])){
            $uk_phonetic = base64_encode($jsonArray['basic']['uk-phonetic']);
        }
        if(isset($jsonArray['basic']['wfs'])){
            $wfs=base64_encode(json_encode($jsonArray['basic']['wfs'],JSON_UNESCAPED_UNICODE));
        }
        if(isset($jsonArray['basic']['explains'])){
            $explains = base64_encode(json_encode($jsonArray['basic']['explains'],JSON_UNESCAPED_UNICODE));
        }
    }
    
    $mysqlExec=sprintf('insert into tb_words_youdao(query,fetch_count,us_phonetic,phonetic,uk_phonetic,wfs,explains) values("%s",%u,"%s","%s","%s","%s","%s")',
        $query,1,$us_phonetic,$phonetic,$uk_phonetic,$wfs,$explains);
    $result=mysqlExecute($dbObj,$mysqlExec);
    if(!$result){
        setLastError(ERR_DB_ACCESS_FAIL);
        return false;
    }else{
        return true;
    }
}

function insertKeywordInfoToKingsoft($dbObj,$jsonArray){

    // try to parse data and into database.
    if(!isset($jsonArray['word_name'])){
        setLastError(ERR_DB_KEYWORD_INVALID);
        return false;
    }
    $query = base64_encode($jsonArray['word_name']);
    $us_phonetic="";
    $phonetic="";
    $uk_phonetic="";
    $exchange="";
    $explains="";
    if(isset($jsonArray['symbols'])){
        foreach($jsonArray['symbols'] as $element){
            if(isset($element['ph_am'])){
                $us_phonetic = base64_encode($element['ph_am']);
            }
            if(isset($element['ph_other'])){
                // need to trim string.
                $phonetic = trimString($element['ph_other'],'http://res-tts.iciba.com');
                $phonetic = base64_encode($phonetic);
            }
            if(isset($element['ph_en'])){
                $uk_phonetic = base64_encode($element['ph_en']);
            }
            if(isset($element['parts'])){
                $explains = base64_encode(json_encode($element['parts'],JSON_UNESCAPED_UNICODE));
            }
        }
    }
    if(isset($jsonArray['exchange'])){
        $exchange=base64_encode(json_encode($jsonArray['exchange'],JSON_UNESCAPED_UNICODE));
    }
    
    $mysqlExec=sprintf('insert into tb_words_kingsoft(query,fetch_count,us_phonetic,phonetic,uk_phonetic,exchange,explains) values("%s",%u,"%s","%s","%s","%s","%s")',
        $query,1,$us_phonetic,$phonetic,$uk_phonetic,$exchange,$explains);
    $result=mysqlExecute($dbObj,$mysqlExec);
    if(!$result){
        setLastError(ERR_DB_ACCESS_FAIL);
        return false;
    }else{
        return true;
    }
}

?>