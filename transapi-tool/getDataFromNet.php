<?php

/**
 * send post method request.
 */
function send_post($url, $post_data) {
  $postdata = http_build_query($post_data);
  $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-type:application/x-www-form-urlencoded',
      'content' => $postdata,
      'timeout' => 5 // 超时时间（单位:s）
    )
  );
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  return $result;
}

function get_data_by_youdaoapi($query){
    $word = $query;
    $salt='1237282';
    $appKey='41c4c3f8031ec44f';
    $key='KyftXK97eCAzLwGkUwW1V7LetZXPUIbp';
    $sign=md5($appKey.$word.$salt.$key);

    $post_data = array(
    'q' => $word,
    'from' => 'EN',
    'to' => 'zh-CHS',
    'appKey'=>$appKey,
    'salt'=>$salt,
    'sign'=>$sign,
    );

    $ret = send_post('http://openapi.youdao.com/api', $post_data);
    return $ret;
}
?>