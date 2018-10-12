<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_system".
 *
 * @property int $id ID
 * @property string $name 名称
 * @property int $status 状态(1可用 2停用）
 * @property int $sort 排序
 */
class System extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'status' => '状态',
            'sort' => '排序',
        ];
    }
}
