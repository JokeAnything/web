<?php

// success
const ERR_CUSTOM_SUCCESS = 0;

// failure
const ERR_CUSTOM_FAILURE = 1;

// common error code base value.
const ERR_CUSTOM_COMMON_BASE = 10000;

// query keyword is invalid
const ERR_QUERY_INVALID_CODE = ERR_CUSTOM_COMMON_BASE + 1;


// database error code base value.
const ERR_CUSTOM_DATABASE_BASE = 20000;

// database connect fail.
const ERR_DB_CONNECT_FAIL = ERR_CUSTOM_DATABASE_BASE + 1;

// database set charset fail.
const ERR_DB_SETCHARSET_FAIL = ERR_CUSTOM_DATABASE_BASE + 2;

// database init fail.
const ERR_DB_INIT_FAIL = ERR_CUSTOM_DATABASE_BASE + 3;

// database access fail.
const ERR_DB_ACCESS_FAIL = ERR_CUSTOM_DATABASE_BASE + 4;

// database keyword invalid.
const ERR_DB_KEYWORD_INVALID = ERR_CUSTOM_DATABASE_BASE + 5;


// netapi error code base value.
const ERR_CUSTOM_NETAPI_BASE = 30000;

// netapi request data is empty.
const ERR_NETAPI_REQUEST_DATA_EMPTY = ERR_CUSTOM_NETAPI_BASE + 1;

// netapi request fail.
const ERR_NETAPI_REQUEST_FAIL = ERR_CUSTOM_NETAPI_BASE + 2;

// netapi request data invalid.
const ERR_NETAPI_REQUEST_DATA_INVALID = ERR_CUSTOM_NETAPI_BASE + 3;

// netapi request data no result.
const ERR_NETAPI_REQUEST_NO_RESULT = ERR_CUSTOM_NETAPI_BASE + 4;

$lastErrCode = ERR_CUSTOM_SUCCESS;

function setLastError($error){
    global $lastErrCode;
    $lastErrCode = $error;
}
function getLastError(){
    global $lastErrCode;
    return $lastErrCode;
}

function echo_last_error(){
    $error = getLastError();
    $str = "{\"error\":$error}";
    echo $str;
}

?>