<?php

    header("Content-Type: text/html;charset=utf-8");
    include_once "transapi_error.php";
    include_once "getDataFromDb.php";
    include_once "getDataFromNet.php";
    
    // define data source type,from database cache.
    const SRC_DATA_TYPE_DB = 0;
    
    // define data source type,from realtime netapi.
    const SRC_DATA_TYPE_NETAPI = 1;

    if(!isset($_REQUEST["query"])){
        setLastError(ERR_QUERY_INVALID_CODE);
        echo_last_error();
        exit(1);
    }

    $queryInput = $_REQUEST["query"];
    
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
        $result = getKeywordInfoFromDB($dbObj,$query);
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
                $lastRes['ukphonetic']=base64_decode($result['uk_phonetic']);
            }
            if($result['wfs']!==""){
                $lastRes['wfs']=json_decode(base64_decode($result['wfs']),true);
            }
            if($result['explains']!==""){
                $lastRes['explains']=json_decode(base64_decode($result['explains']),true);
            }
            $lastRes['ds']=SRC_DATA_TYPE_DB;
            $lastRes['error']=ERR_CUSTOM_SUCCESS;
            $jsonstr = json_encode($lastRes,JSON_UNESCAPED_UNICODE);
            
            // respond data to requester.
            echo $jsonstr;
        }else{
            $api_original = get_data_by_youdaoapi($queryInput);
            if($api_original===""){
                setLastError(ERR_NETAPI_REQUEST_DATA_EMPTY);
                echo_last_error();
                exit(1);
            }
            $api_data = json_decode($api_original,true);
            if($api_data['errorCode'] !== '0'){
                setLastError(ERR_NETAPI_REQUEST_FAIL);
                echo_last_error();
                exit(1);
            }else{
                if(!isset($api_data['query'])){
                    setLastError(ERR_NETAPI_REQUEST_DATA_INVALID);
                    echo_last_error();
                    exit(1);
                }else{
                    $lastRes_api['query'] = $api_data['query'];
                }
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
                    if(isset($api_data['basic']['wfs'])){
                        $lastRes_api['wfs'] = $api_data['basic']['wfs'];
                    }
                    if(isset($api_data['basic']['explains'])){
                        $lastRes_api['explains'] = $api_data['basic']['explains'];
                    }
                    $lastRes_api['ds']=SRC_DATA_TYPE_NETAPI;
                    $lastRes_api['error']=ERR_CUSTOM_SUCCESS;
                    $jsonstr_api = json_encode($lastRes_api,JSON_UNESCAPED_UNICODE);
                    // data cache into database for next time.
                    insertKeywordInfoToDB($dbObj,$api_data);
                    // respond data to requester.
                    echo $jsonstr_api;
                }else{
                    setLastError(ERR_NETAPI_REQUEST_NO_RESULT);
                    echo_last_error();
                }
            }
        }
        unInitDataBase($dbObj);
    }else{
        setLastError(ERR_DB_INIT_FAIL);
        echo_last_error();
    }
?>