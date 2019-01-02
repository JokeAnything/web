<?php
function connectMySql($host,$user,$password){
    $link = mysqli_connect($host, $user, $password, "db_youdaocld_api_cache");
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
function getKeywordInfoFromDB($dbObj,$query)
{
    $query_sql='select * from words where query="'.$query.'"';
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

function insertKeywordInfoToDB($dbObj,$jsonArray){

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
    
    $mysqlExec=sprintf('insert into words(query,us_phonetic,phonetic,uk_phonetic,wfs,explains,fetch_count) values("%s","%s","%s","%s","%s","%s",%u)',
        $query,$us_phonetic,$phonetic,$uk_phonetic,$wfs,$explains,1);
    $result=mysqlExecute($dbObj,$mysqlExec);
    if(!$result){
        setLastError(ERR_DB_ACCESS_FAIL);
        return false;
    }else{
        return true;
    }
}

?>