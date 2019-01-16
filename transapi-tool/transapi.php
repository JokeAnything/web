<?php

    header("Content-Type: text/html;charset=utf-8");
    include_once "transapi_error.php";
    include_once "get_data_from_db.php";
    include_once "get_data_youdao_api.php";
    include_once "get_data_kingsoft_api.php";
    include_once "utils.php";
    
    // define data source type,from database cache.
    const SRC_DATA_TYPE_DB = 0;
    
    // define data source type,from realtime netapi-youdao.
    const SRC_DATA_TYPE_NETAPI_YOUDAO = 1;

    // define data source type,from realtime netapi-youdao.
    const SRC_DATA_TYPE_NETAPI_KINGSOFT = 2;

    if(!isset($_REQUEST["query"])){
        setLastError(ERR_QUERY_INVALID_CODE);
        echo_last_error();
        exit(1);
    }


    $queryInput = $_REQUEST["query"];
    
//    $queryInput = $argv[1];
    
    // get data from DB.
    if( $queryInput === ''){
        setLastError(ERR_QUERY_INVALID_CODE);
        echo_last_error();
        exit(1);
    }

    $queryInput=strtolower($queryInput);
    
    $dbObj = initDataBase();
    if($dbObj){
        $query = base64_encode($queryInput);
        $data_src = SRC_API_KINGSOFT;
        $result = getKeywordInfoFromDB($dbObj,$query,$data_src);
        if($result){
            if($result['query']!==""){
                $lastRes['query']=base64_decode($result['query']);
            }
            if($result['us_phonetic']!==""){
                $lastRes['us_phonetic']=base64_decode($result['us_phonetic']);
            }
            if($result['phonetic']!==""){
                $lastRes['phonetic']=base64_decode($result['phonetic']);
            }
            if($result['uk_phonetic']!==""){
                $lastRes['uk_phonetic']=base64_decode($result['uk_phonetic']);
            }
            $lastRes['ds']=SRC_DATA_TYPE_DB;
            $lastRes['error']=ERR_CUSTOM_SUCCESS;
            $jsonstr = json_encode($lastRes,JSON_UNESCAPED_UNICODE);
            
            // respond data to requester.
            echo $jsonstr;
        }else{
            if(!try_get_data_from_kingsoft_api($dbObj,$queryInput)){
                try_get_data_from_youdao_api($dbObj,$queryInput);
            }
        }
        unInitDataBase($dbObj);
    }else{
        setLastError(ERR_DB_INIT_FAIL);
        echo_last_error();
    }
    
function try_get_data_from_youdao_api($dbObj,$qw){
    $api_original = get_data_by_youdaoapi($qw);
    if($api_original===""){
        setLastError(ERR_NETAPI_REQUEST_DATA_EMPTY);
        echo_last_error();
        return false;
    }
    $api_data = json_decode($api_original,true);
    if($api_data['errorCode'] !== '0'){
        setLastError(ERR_NETAPI_REQUEST_FAIL);
        echo_last_error();
        return false;
    }else{
        if(!isset($api_data['query'])){
            setLastError(ERR_NETAPI_REQUEST_DATA_INVALID);
            echo_last_error();
            return false;
        }else{
            $lastRes_api['query'] = $api_data['query'];
        }
        // data cache into database for next time.
        insertKeywordInfoToDB($dbObj,$api_data,SRC_API_YOUDAO);

        if(isset($api_data['basic'])){
            if(isset($api_data['basic']['us-phonetic'])){
                $lastRes_api['us_phonetic'] = $api_data['basic']['us-phonetic'];
            }
            if(isset($api_data['basic']['phonetic'])){
                $lastRes_api['phonetic'] = $api_data['basic']['phonetic'];
            }
            if(isset($api_data['basic']['uk-phonetic'])){
                $lastRes_api['uk_phonetic'] = $api_data['basic']['uk-phonetic'];
            }
            $lastRes_api['ds']=SRC_DATA_TYPE_NETAPI_YOUDAO;
            $lastRes_api['error']=ERR_CUSTOM_SUCCESS;
            $jsonstr_api = json_encode($lastRes_api,JSON_UNESCAPED_UNICODE);
            // respond data to requester.
            echo $jsonstr_api;
            return true;
        }else{
            setLastError(ERR_NETAPI_REQUEST_NO_RESULT);
            echo_last_error();
            return false;
        }
    }
}

function try_get_data_from_kingsoft_api($dbObj,$qw){
    $api_original = get_data_by_acibaapi($qw);
    if($api_original ===""){
        setLastError(ERR_NETAPI_REQUEST_DATA_EMPTY);
        echo_last_error();
        return false;
    }
    $api_data = json_decode($api_original,true);

    if(!isset($api_data['word_name'])){
        setLastError(ERR_NETAPI_REQUEST_DATA_INVALID);
        echo_last_error();
        return false;
    }else{
        $lastRes_api['query'] = $api_data['word_name'];
    }
    
    // data cache into database for next time.
    insertKeywordInfoToDB($dbObj,$api_data,SRC_API_KINGSOFT);

    if(isset($api_data['symbols'])){
        foreach($api_data['symbols'] as $element){
            if(isset($element['ph_am'])){
                $lastRes_api['us_phonetic'] = $element['ph_am'];
            }
            if(isset($element['ph_other'])){
                $lastRes_api['phonetic'] = trimString($element['ph_other'],'http://res-tts.iciba.com');
            }
            if(isset($element['ph_en'])){
                $lastRes_api['uk_phonetic'] = $element['ph_en'];
            }
            $lastRes_api['ds']=SRC_DATA_TYPE_NETAPI_KINGSOFT;
            $lastRes_api['error']=ERR_CUSTOM_SUCCESS;
            $jsonstr_api = json_encode($lastRes_api,JSON_UNESCAPED_UNICODE);
            
            // respond data to requester.
            echo $jsonstr_api;
            return true;
        }
    }
    setLastError(ERR_NETAPI_REQUEST_NO_RESULT);
    echo_last_error();
    return false;
}
?>