<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_tag".
 *
 * @property int $id ID
 * @property string $name 标签名称
 * @property int $type 类别（0 工单标签 1 文章标签）
 * @property int $count 数量(只要在表中出现，至少为1 所以默认1）
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'count'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['name', 'type'], 'unique', 'targetAttribute' => ['name', 'type']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标签名称',
            'type' => '类别（0 工单标签 1 文章标签）',
            'count' => '数量(只要在表中出现，至少为1 所以默认1）',
        ];
    }
}
