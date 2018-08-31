<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_user_system".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $system_id 工单所属系统ID
 */
class UserSystem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_user_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'system_id'], 'required'],
            [['user_id', 'system_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'system_id' => '工单所属系统ID',
        ];
    }
}
