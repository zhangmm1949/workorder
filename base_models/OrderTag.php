<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_order_tag".
 *
 * @property int $order_id 工单号
 * @property string $tag 单个标签
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
            [['order_id'], 'integer'],
            [['tag'], 'string', 'max' => 20],
            [['order_id', 'tag'], 'unique', 'targetAttribute' => ['order_id', 'tag']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '工单号',
            'tag' => '单个标签',
        ];
    }
}
