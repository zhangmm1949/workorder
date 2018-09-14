<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-9-14
 * Time: 下午4:29
 */

namespace app\commands;

use app\models\Order;
use yii\console\Controller;
use Yii;

class OrderController extends Controller
{
    /**
     * 向工单负责人发送邮件
     * @throws \yii\db\Exception
     */
    public function actionSendMail()
    {
        $info = $this->getSuspendingOrders();
        if ($info){
            $order_ids = [];
            $mailer = Yii::$app->mailer->compose();
            foreach ($info as $v){
                $body = '您有一个工单[ ' . $v['title'] . ' ] 需要处理， ID: ' . $v['id'];
                $ret = $mailer
                    ->setTo($v['email'])
                    ->setSubject('工单提示')
                    ->setHtmlBody($body)
                    ->send();
                if ($ret===true){
                    $order_ids[] = $v['id'];
                }
            }
            if (!empty($order_ids)){
                $ids = implode(',', $order_ids);
                $sql = "UPDATE xm_order SET `is_mail`=1 WHERE `id` IN ($ids);";
                Yii::$app->db->createCommand($sql)->execute();
            }
        }

    }

    /**
     * 获取未解决但已分配了负责人的工单
     * @return array
     * @throws \yii\db\Exception
     */
    public function getSuspendingOrders()
    {
        $sql = "SELECT o.id, o.title, u.user_name, u.email
FROM xm_order o
INNER JOIN xm_user u ON u.id = o.solve_user
WHERE o.`status` IN (0,10)
AND o.solve_user > 0
AND o.is_mail = 0;";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

}