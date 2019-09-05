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
    /**
     * redis 列表key
     */
    const LOG_REDIS_KEY = 'log_list_key';

    /**
     * 日志表
     */
    const TABLE_NAME = 'xm_log';

    /**
     * @var int
     */
    private static $max_lenth  = 20000;

    /**
     * @param string $level
     * @param $index
     * @param $msg
     * @param $request
     * @param $response
     */
    private static function record($level='log', $index, $msg, $request, $response)
    {
        $ip = self::getUserIp();
        $user_id = Yii::$app->user->id ? Yii::$app->user->id : 0;
        $user_name = $user_id ? User::findOne($user_id)->user_name : 'Guest';

        // 数据保持原样，不进行Unicode编码  $request 和 $request 有最大长度限制
        $index = is_string($index) ? $index : json_encode($index, JSON_UNESCAPED_UNICODE);
        $msg = is_string($msg) ? $msg : json_encode($msg, JSON_UNESCAPED_UNICODE);
        $request = empty($request) ? '' : (is_string($request) ? mb_substr($request,0,self::$max_lenth,'utf-8') : mb_substr(json_encode($request, JSON_UNESCAPED_UNICODE),0,self::$max_lenth,'utf-8'));
        $response = empty($response) ? '' : (is_string($response) ? mb_substr($response,0,self::$max_lenth,'utf-8') : mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE),0,self::$max_lenth,'utf-8'));

//        $url = self::getUrl();
        $url = PHP_SAPI == 'cli' ? 'cli' : Yii::$app->request->url;

        $action = '';
        $line = 0;
        $file = '';
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5); # 方法调用只追溯五层
        if (!empty($backtrace) && is_array($backtrace)) {
            foreach ($backtrace as $key => $item) {
                if ($item['function'] == 'call_user_func_array') {
                    break;
                }
                if ($key < 1) {
                    continue;
                }
                if ($key == 1) {
                    $file = $item['file'];
                    $line = $item['line'];
                }
                $action = '->' . $item['function'] . $action;
            }
        }
        $action = trim($action, '->');

    }

    # 写入数据库
    private static function writeLog()
    {

    }

    /**
     * @return array|false|string
     */
    private static function getUserIp()
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

    /**
     * 记录日志
     * 说明：通用记录日志的方法
     * @param string $level    日志等级
     * @param string $index    自定义索引
     * @param string $msg      消息体
     * @param string $request  请求体
     * @param string $response 响应体
     */
    public static function log($level = 'log', $index = '', $msg = '', $request = '', $response = '')
    {
        self::record($level, $index, $msg, $request, $response);
    }

}