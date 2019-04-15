<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 发起http请求并获取返回数据
 *
 * @param string $url get请求地址
 * @param int $http_code 返回状态码
 * @return mixed
 */
 function curl_get($url, &$http_code = 0)
 {
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

     //不做证书校验，部署在linux环境下请改为true
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
     $file_contents = curl_exec($ch);
     $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     curl_close($ch);

     return $file_contents;
 }
 /**
  *
  * 发送http POST请求
  *
  * @param string $url, 请求地址
  * @param array $params 参数
  * @return mixed
  */
  function curl_post($url, array $params = [])
  {
      $data_string = json_encode($params);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

      $data = curl_exec($ch);
      curl_close($ch);

      return $data;
  }

/**
 * 生成随机字符串
 *
 * @param int $length 随机串长度
 * @return string $str 生成的随机字符串
 */
function getRandChar($length)
{
    $str = null;
    $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($str_pol) - 1;
    for($i = 0; $i< $length; $i++) $str .= $str_pol[rand(0, $max)];

    return $str;
}
