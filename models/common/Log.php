<?php
/**
 * Created by PhpStorm.
 * User: mi
 * Date: 2019/8/26
 * Time: 13:44
 */

namespace app\models\common;

use Yii;
use app\models\User;

class Log
{
    private $level = ['log', 'error', 'debug', 'waring'];

    private static $max_lenth  = 20000;

    #记录日志到redis
    private static function record($level='log', $index, $msg, $request, $response)
    {
        $ip = self::getUserIp();
        $user_id = Yii::$app->user->id ? Yii::$app->user->id : 0;
        $user_name = $user_id ? User::findOne($user_id)->user_name : 'Guest';
        $index = is_string($index) ? $index : json_encode($index, JSON_UNESCAPED_UNICODE);
        $msg = is_string($msg) ? $msg : json_encode($msg, JSON_UNESCAPED_UNICODE);
        $request = empty($request) ? '' : (is_string($request) ? mb_substr($request,0,self::$max_lenth,'utf-8') : mb_substr(json_encode($request, JSON_UNESCAPED_UNICODE),0,self::$max_lenth,'utf-8'));
        $response = empty($response) ? '' : (is_string($response) ? mb_substr($response,0,self::$max_lenth,'utf-8') : mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE),0,self::$max_lenth,'utf-8'));
        $url = self::getUrl();
        $action = '';





    }

    # 写入数据库
    private static function writeLog()
    {

    }

    public static function getUserIp()
    {
        if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])) {
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        } elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"])) {
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknown";
        }
        if ($ip == "::1") {
            $ip = '127.0.0.1';
        }
        $arr = explode(',', $ip);
        if (empty($arr)) {
            return $ip;
        }
        return $arr[0];
    }

    /**
     * 得到请求url
     * @return string
     */
    private static function getUrl()
    {
        if (PHP_SAPI == 'cli'){
            return 'cli';
        }

        if (empty($_SERVER['HTTP_REFERER'])) {

            $scheme = '';
            if (!empty($_SERVER['REQUEST_SCHEME'])) {
                $scheme = $_SERVER['REQUEST_SCHEME'] . '://';
            }

            $host = '';
            if (!empty($_SERVER['HTTP_HOST'])) {
                $host = $_SERVER['HTTP_HOST'];
            }

            $request_url = '';
            if (!empty($_SERVER['REQUEST_URI'])) {
                $request_url = $_SERVER['REQUEST_URI'];
            }

            $url = $scheme . $host . $request_url;

        } else {
            $url = $_SERVER['HTTP_REFERER'];
        }

        return $url;
    }

}