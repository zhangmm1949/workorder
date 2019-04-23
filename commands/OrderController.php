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
use app\models\common\ExcelHelper;

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

    public function actionDoo()
    {
        // 定时任务  2 * * * * * php /home/imor/www/doolocal/yii order/doo

        $name = date('His');
        $sql = "UPDATE xm_tag SET `name`= $name WHERE id = 166;";
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 工单导出
     */
    public function actionOrderExport()
    {
        $sql = "SELECT o.order_sn, u.user_name, IF(o.`status` = 20, '已完成', IF(o.`status`=10, '处理中', '待处理')) AS order_status, FROM_UNIXTIME(o.present_time) add_time, FROM_UNIXTIME(o.update_time) update_time, 
s.`name` AS system, CASE o.classify WHEN 1 THEN '系统Bug' WHEN 2 THEN '系统Bug' WHEN 3 THEN '新需求' WHEN 4 THEN '导入导出/帮助类' WHEN 5 THEN '遗留/需排期' ELSE '待确定' END AS 问题分类, o.title, o.content, o.remark
FROM xm_order o
LEFT JOIN xm_system s ON s.id = o.system
LEFT JOIN xm_user u ON u.id = o.present_user
WHERE 1
AND o.present_time > UNIX_TIMESTAMP(20190415);";

        $ret = Yii::$app->db->createCommand($sql)->queryAll();
//        var_dump($ret);

        $header = [
            ['field' => 'order_sn',    'title' => 'order_sn', 'type' => 'string'],
            ['field' => 'user_name',    'title' => '创建人', 'type' => 'string'],
            ['field' => 'order_status',     'title' => '状态', 'type' => 'string'],
            ['field' => 'add_time',     'title' => '创建时间', 'type' => 'string'],
            ['field' => 'update_time',     'title' => '最后更新时间', 'type' => 'string'],
            ['field' => 'system',     'title' => '系统', 'type' => 'string'],
            ['field' => '问题分类',     'title' => '问题分类', 'type' => 'string'],
            ['field' => 'title',     'title' => '标题', 'type' => 'string'],
            ['field' => 'content',     'title' => '内容', 'type' => 'string'],
            ['field' => 'remark',     'title' => '原因&处理', 'type' => 'string'],
        ];

        $file_name = 'data' . date('mdHi') . '.csv';
        $dir       = Yii::$app->basePath . '/runtime/data/';

        if (!empty($ret)) {
            foreach ($ret as $k=>$v){
                $ret[$k]['content'] = strip_tags($v['content']);
            }

            ExcelHelper::export2DArrayByCSV($ret, $header, $file_name, $dir, true, $append = true);
            echo "done";
        } else {
            var_dump($ret);
            die();
        }

    }

}