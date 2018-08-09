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
     * @param $obj object 传递过来的对象
     * @param $action string 操作类型 insert/update/delete
     * @return bool 实际处理的方法的结果
     */
    public function updateTags($obj, $action)
    {
        $params = [];
        $tags = explode('|', $obj->tags);
        if (empty($tags) && $action <> 'update') return true;

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

    /**
     * 新建文档（博客/工单等）对象时对标签的操作
     * @param $params
     * @throws \yii\db\Exception
     */
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

    /**
     * 删除文档（博客/工单等）对象时对标签的操作
     * @param $params
     * @throws \yii\db\Exception
     */
    private function tagDelete($params)
    {
        foreach ($params['tags'] as $tag){
            $sql = "UPDATE `xm_tag` SET `count`=`count`-1 WHERE `name`='$tag' AND `type`=$params[type];";
            Yii::$app->db->createCommand($sql)->execute();
        }
        $sql = "DELETE FROM `$params[join_table]` WHERE `$params[join_column]` = $params[join_column_value];";
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 编辑文档（博客/工单等）对象时对标签的操作
     * @param $params
     * @return bool
     * @throws \yii\db\Exception
     */
    private function tagUpdate($params)
    {
        //查询原有tag
        $sql = "SELECT t.`name` FROM `$params[join_table]` j INNER JOIN `xm_tag` t ON t.`id` = j.`tag_id` WHERE j.`$params[join_column]` = $params[join_column_value];";
        $ret = Yii::$app->db->createCommand($sql)->queryAll();
        $old_tags = array_column($ret, 'name');

        // 比较新提交的tag 跟 原有tag 如果没有区别直接返回不进行处理
        if (empty(array_diff($old_tags, $params['tags']))) return true;

        $params2 = $params;
        $params2['tags'] = $old_tags;

        $this->tagDelete($params2); // 删除旧tag
        if (!empty($params['tags'])){ // 更新时会传空值
            $this->tagInsert($params);  // 写入新tag
        }

    }

    /**
     * 返回按数量倒序排列的标签
     * @param $type
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getSortTag($type)
    {
        $type = $type=='order' ? 0 : 1;
        return static::find()->asArray()
            ->where(['xm_tag.type' => $type])
            ->orderBy('`count` DESC')
            ->limit(10)
            ->all();
    }

}