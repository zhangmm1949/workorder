<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-7-18
 * Time: 下午12:10
 */

namespace app\models;

use app\base_models\Tag as FaTag;
use Yii;
use app\base_models\OrderTag;


class Tag extends FaTag
{
    public function rules()
    {
        return [
            [['type', 'count'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['name', 'type'], 'unique', 'targetAttribute' => ['name', 'type']],
        ];
    }

    public function setOrderTag($type, $id, $tags, $action)
    {
        $tag_arr = explode('|', $tags);
        $order_tag = new OrderTag();
        foreach ($tag_arr as $item){
            $ret = static::findOne(['name'=>$item]);
            if (!$ret){
                $this->name = $item;
                $this->type = $type=='order' ? 0 : 1;
                $this->count = 1;
                $this->save();
                /*$order_tag->order_id = $id;
                $order_tag->tag_id = $this->id;
                $order_tag->save();*/
            }else{
                var_dump($ret);
                switch ($action){
                    case 'insert':
                        $ret->count += 1;
                        $ret->save();
                        break;
                    case 'update':
                        $ret->save();
                        break;
                    case 'delete':
                }
            }
        }
    }

}