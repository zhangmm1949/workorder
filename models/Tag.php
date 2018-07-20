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

    public function updateTag($type, $id, $tags, $action)
    {
        $func_name = 'set' . ucfirst(trim($type)) . 'Tag';
        $this->$func_name($id, $tags, $action);
    }

    private function setOrderTag($order_id, $tags, $action){
        $tag_arr = explode('|', $tags);
        $order_tag = new OrderTag();

        switch ($action){
            case 'insert':
                foreach ($tag_arr as $item){
                    $ret = static::findOne(['name'=>$item]);
                    if (!$ret){
                        $this->name = $item;
                        $this->type = 0;
                        $this->count = 1;
                        $this->save();
                        $order_tag->tag_id = $this->id;
                    }else{
                        $ret->count += 1;
                        $ret->save();
                        $order_tag->tag_id = $ret->id;
                    }

                    $order_tag->order_id = $order_id;
                    $order_tag->save();
                }
                break;

            case 'delete':
                OrderTag::deleteAll(['order_id'=>$order_id]);
                foreach ($tag_arr as $item){
                    $ret = static::findOne(['name'=>$item]);
                    $ret->count -=1;
                    $ret->save();
                }
                break;

            case 'update':
                break;
        }
    }

    private function setBlogTag()
    {

    }

    public static function getSortTag($type)
    {
        $type = $type=='order' ? 0 : 1;
        return static::find()->asArray()
            ->where(['xm_tag.type' => $type])
            ->orderBy('count DESC')
            ->limit(10)
            ->all();
    }

}