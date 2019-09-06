<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2019/9/6
 * Time: 15:46
 */

namespace app\commands;


use app\models\RegisterForm;
use yii\base\Exception;
use yii\console\Controller;
use Yii;

class ToolController extends Controller
{
    public function actionAddUser()
    {
        $csv = <<<CSV
陈海东,chenhaidong1@xiaomi.com
刘超凡,liuchaofan@xiaomi.com
CSV;
        $arr = explode(PHP_EOL, $csv);
        foreach ($arr as $item){
            $info = explode(',', $item);

            $model = new RegisterForm();
            $model->user_name = $info[0];
            $model->email = $info[1];
            $model->password = '123456';
            $model->re_password = '123456';
            $model->department_id = 1;
            $model->user_systems = [9];

            if ($model->register()) {
                echo $model->user_name . '----- ok' . PHP_EOL;
            }else{
                var_dump($model);
            }
        }
    }
}