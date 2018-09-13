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

    public static function UpdateOrderTag($event)
    {
        $order = $event->sender;
        $action = $event->data;
        switch ($action){
            case 'insert' :
                self::insertOrderTags($order->id, $order->tags);
                break;
            case 'update' :
                self::deleteAll(['order_id'=>$order->id]);
                self::insertOrderTags($order->id, $order->tags);
                break;
            case 'delete' :
                self::deleteAll(['order_id'=>$order->id]);
                break;
            default :
                return true;
        }
        return true;
    }

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
}