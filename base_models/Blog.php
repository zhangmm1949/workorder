<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_blog".
 *
 * @property int $id
 * @property string $title 标题
 * @property int $category_id 分类
 * @property string $content 内容
 * @property int $create_at 创建时间
 * @property int $update_at 最后更新时间
 * @property int $publish_at 发布时间
 * @property string $tag 标签 是否启用待定
 * @property int $display 是否可见（0 否，1 是）
 */
class Blog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_blog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'create_at', 'update_at', 'publish_at', 'display'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['title', 'tag'], 'string', 'max' => 100],
            [['title'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'category_id' => '分类',
            'content' => '内容',
            'create_at' => '创建时间',
            'update_at' => '最后更新时间',
            'publish_at' => '发布时间',
            'tag' => '标签 是否启用待定',
            'display' => '是否可见（0 否，1 是）',
        ];
    }
}
