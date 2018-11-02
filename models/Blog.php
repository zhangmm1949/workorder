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
    public $publishAt;

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


}