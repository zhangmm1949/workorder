<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_order_tag".
 *
 * @property int $id
 * @property int $order_id
 * @property int $tag_id
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
            [['order_id', 'tag_id'], 'required'],
            [['order_id', 'tag_id'], 'integer'],
            [['order_id', 'tag_id'], 'unique', 'targetAttribute' => ['order_id', 'tag_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'tag_id' => 'Tag ID',
        ];
    }
}
