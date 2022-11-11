<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-9-17
 * Time: 下午8:54
 */

namespace app\models;


class Blog extends \app\base_models\Blog
{
    protected $publishAt;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'create_at', 'update_at', 'publish_at', 'display'], 'integer'],
            [['content','publishAt'], 'required'],
            [['content','publishAt'], 'string'],
            [['title', 'tag'], 'string', 'max' => 100],
            [['title'], 'unique'],
        ];
    }

    public function beforeSave($insert)
    {
        $this->publish_at = strtotime($this->publishAt);
        if (parent::beforeSave($insert)){
            if ($this->isNewRecord){
                $this->create_at = time();
            }else{
                $this->update_at = time();
            }
            return true;
        }
        return false;
    }

    public function getPublishAt()
    {
        if (is_null($this->publishAt)){
            $this->publishAt = date('Y-m-d H:i:s', $this->publish_at);
        }
        return $this->publishAt;
    }

    public static function getCategory()
    {
        return [
            1 => '时事热点',
            2 => '天下杂谈',
            3 => '原创'
        ];
    }


}