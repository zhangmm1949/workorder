<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $auth_key
 * @property string $password
 * @property string $email
 * @property string $tel
 * @property integer $department_id
 * @property integer $status
 * @property integer $created_at
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xm_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_key', 'department_id', 'created_at'], 'required'],
            [['department_id', 'status', 'created_at'], 'integer'],
            [['user_name'], 'string', 'max' => 30],
            [['auth_key'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 60],
            [['tel'], 'string', 'max' => 20],
            [['user_name'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => '用户名',
            'auth_key' => 'Auth Key',
            'password' => '密码',
            'email' => '邮箱',
            'tel' => '手机',
            'department_id' => '所属部门',
            'status' => '状态（1可用，2不可用）',
            'created_at' => '创建时间',
        ];
    }
}
