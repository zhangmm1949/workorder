<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2019/9/19
 * Time: 17:54
 */

namespace app\commands;


use yii\console\Controller;
use app\models\common\Log;

class LogController extends Controller
{
    /**
     * 定时任务 日志从 redis 写入数据库
     */
    public function actionWriteLog()
    {
        Log::writeLog();
    }
}