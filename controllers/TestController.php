<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 19-1-29
 * Time: 上午10:40
 */

namespace app\controllers;


use yii\web\Controller;
use app\models\UserSystem;

class TestController extends Controller
{
    public function actionIndex()
    {
        var_dump(\Yii::$app->test->getSystemIdsByUserId(1));
    }
}