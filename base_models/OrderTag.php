<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_order_tag".
 *
 * @property int $order_id 工单ID
 * @property string $tag 标签
 * @property int $add_time 添加时间
 */
class OrderTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_order_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'tag'], 'required'],
            [['order_id', 'add_time'], 'integer'],
            [['tag'], 'string', 'max' => 50],
            [['order_id', 'tag'], 'unique', 'targetAttribute' => ['order_id', 'tag']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '工单ID',
            'tag' => '标签',
            'add_time' => '添加时间',
        ];
    }
}
