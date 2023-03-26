<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-8-7
 * Time: 上午9:44
 */

namespace app\models;

use app\base_models\OrderTag as FaOrderTag;
use Yii;

class OrderTag extends FaOrderTag
{
    public $num;

    /**
     * 工单标签的增删改
     * @param $event object Event事件
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function UpdateOrderTag($event)
    {
        $order = $event->sender;
        $action = $event->data;
        switch ($action){
            case 'insert' :
                self::insertOrderTags($order->id, $order->tags);
                break;
            case 'update' :
                # 如果新旧tag 不一致则更新
                if (!self::checkTags($order->id, $order->tags)){
                    self::deleteAll(['order_id'=>$order->id]);
                    self::insertOrderTags($order->id, $order->tags);
                }
                break;
            case 'delete' :
                self::deleteAll(['order_id'=>$order->id]);
                break;
            default :
                return true;
        }
        return true;
    }

    /**
     * @param $order_id int 工单ID
     * @param $tags string 格式化后的标签
     * @return bool|int
     * @throws \yii\db\Exception
     */
    private static function insertOrderTags($order_id, $tags)
    {
        if (!$tags) return true; //为空直接返回 不向数据库写入

        $tag_arr = explode('|', $tags);
        $rows = [];
        foreach ($tag_arr as $k => $v){
            $rows[$k]['order_id'] = $order_id;
            $rows[$k]['tag'] = $v;
        }
        return Yii::$app->db->createCommand()->batchInsert(OrderTag::tableName(), ['order_id', 'tag'], $rows)->execute();
    }

    /**
     * 检查原有tag和新传来的tag是否一致
     * @param int $order_id
     * @param string $new_tags
     * @return bool
     */
    private static function checkTags(int $order_id, string $new_tags)
    {
        # 使用数组来比较，因为拼接字符串可能会导致因顺序不一致而差错
        $sql = 'select tag from xm_order_tag where order_id=' . $order_id;
        $old_tags = self::findBySql($sql)->asArray()->all();
        $old_tags = array_column($old_tags, 'tag');
        $new_tags = explode('|', $new_tags);
        if ($new_tags == $old_tags){  // 使用 == 判断 即使键不一致也认为是相同的数组
            return true;
        }
        return false;
    }

}