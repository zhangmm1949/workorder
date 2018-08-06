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
    public $tag_type = [
        'xm_order' => 0,
        'xm_blog' => 1,
    ];

    public function rules()
    {
        return [
            [['type', 'count'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['name', 'type'], 'unique', 'targetAttribute' => ['name', 'type']],
        ];
    }

    /**
     * @param $obj object 数据对象
     * @param $action string 操作类型， update/insert/delete
     */
    public function updateTags($obj, $action)
    {
        $params = [];
        $tags = explode('|', $obj->tags);
        if (empty($tags)) return true;

        $params['tags'] = $tags;

        $table_full_name = $obj->tableName();

        $params['type'] = $this->tag_type[$table_full_name];// 标签类型

        $params['join_table'] = $table_full_name . '_tag';//关联表名称 （xm_order_tag / xm_blog_tag）
        $params['join_column'] = substr($table_full_name, 3) . '_id'; //关联列名 （order_id  blog_id）

        // 对象的主键值
        $obj_primerKey = $obj->primaryKey()[0];
        $obj_primerKey_value = $obj->$obj_primerKey;
        $params['join_column_value'] = $obj_primerKey_value;

        $func_name = 'tag' . ucfirst($action);
        return $this->$func_name($params);
    }

    private function tagInsert($params)
    {

        foreach ($params['tags'] as $tag){
            $sql = "INSERT INTO `xm_tag` (`name`, `type`, `count`) VALUES ('$tag', $params[type], '1') ON DUPLICATE KEY UPDATE `count`=`count`+1;";
            Yii::$app->db->createCommand($sql)->execute();
            $tag_id = Yii::$app->db->getLastInsertID();
            $sql = "INSERT INTO `$params[join_table]` (`$params[join_column]`, `tag_id`) VALUES ($params[join_column_value], $tag_id);";
            Yii::$app->db->createCommand($sql)->execute();
        }
    }

    private function tagDelete($params)
    {
        foreach ($params['tags'] as $tag){
            $sql = "UPDATE `xm_tag` SET `count`=`count`-1 WHERE `name`='$tag' AND `type`=$params[type];";
            Yii::$app->db->createCommand($sql)->execute();
            $sql = "DELETE FROM `$params[join_table]` WHERE `$params[join_column]` = $params[join_column_value];";
            Yii::$app->db->createCommand($sql)->execute();
        }
    }

    private function tagUpdate($params)
    {
        $this->tagDelete($params);
        $this->tagInsert($params);
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