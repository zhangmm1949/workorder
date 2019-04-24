<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 19-1-29
 * Time: 上午10:40
 */

namespace app\controllers;


use Yii;
use yii\db\Exception;
use yii\web\Controller;
use app\models\UserSystem;

class TestController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actionIndex()
    {
        phpinfo();
    }

    public function actionInvoice()
    {
//        $this->layout = false;
        $str = trim(Yii::$app->request->post('invoice_money'));
        $arr = array_filter(explode(' ', $str));


        var_dump($arr);

        $back = $this->getInvoice($arr, 440);


        return $this->render('invoice');
    }

    private function getInvoice($money_arr, $total)
    {

    }

    public function actionRedis()
    {
        $redis = Yii::$app->redis;

        $key1 = 'key1';
        $key2 = 'key2';
        $value1 = 'value1';
        $value2 = 'value2';
//        $value = 1;
        var_dump($redis->hmset('hash', $key1, $value1, $key2, $value2));
//        var_dump($redis->ttl($key));
        var_dump($redis->hset('hash', $key1, 'asas'));
        var_dump($redis->hgetall('hash'));
        var_dump($redis->hmget('hash', $key1));
        var_dump($redis->hkeys('hash'));
        var_dump($redis->hvals('hash'));
    }

    public function actionCache()
    {
        $cache = \Yii::$app->cache;
        var_dump($cache);
    }

    public function actionSendMails()
    {
        $users = ['704369798@qq.com'];
        $messages = [];
        foreach ($users as $k => $user) {
            $messages[] = Yii::$app->mailer->compose()
                ->setTo($user)
                ->setSubject('测试主题' . $k)
                ->setHtmlBody('测试内容' . $k);
        }
        $ret = Yii::$app->mailer->sendMultiple($messages);
        var_dump($ret);
    }

    public function actionTrans()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try{
            $sql = "UPDATE test SET goods_count = 9 WHERE mi_id = 59614250 AND add_month = 201805;";
            $ret = Yii::$app->db->createCommand($sql)->execute();

//            var_dump($ret);

            /*$sql = "UPDATE test SET goods_count = 90 WHERE mi_ids = 1468289480 AND add_month = 201805;";
            $ret = Yii::$app->db->createCommand($sql)->execute();
            var_dump($ret);
            exit('error');*/

//            $transaction->commit();

        }catch (Exception $e){
            echo $e->getMessage();
            $transaction->rollBack();
        }
    }

    public function actionTrans2()
    {
        //$transaction = Yii::$app->db->beginTransaction();

        try{
            $sql = "UPDATE test SET goods_count = 999 WHERE mi_id = 1468289480 AND add_month = 201805;";
            $ret = Yii::$app->db->createCommand($sql)->execute();
            /*$sql = "UPDATE test SET goods_count = 90 WHERE mi_ids = 1468289480 AND add_month = 201805;";
            $ret = Yii::$app->db->createCommand($sql)->execute();
            var_dump($ret);
            exit('error');*/

//            $transaction->commit();

        }catch (Exception $e){
            echo $e->getMessage();
//            $transaction->rollBack();
        }


    }

}