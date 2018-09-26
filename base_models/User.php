<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_user".
 *
 * @property int $id ID
 * @property string $user_name 用户名
 * @property string $auth_key
 * @property string $password 密码
 * @property string $email 邮箱
 * @property string $tel 手机
 * @property int $department_id 所属部门
 * @property int $status 状态
 * @property int $created_at 创建时间
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_id', 'status', 'created_at'], 'integer'],
            [['created_at'], 'required'],
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
     * {@inheritdoc}
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
            'status' => '状态',
            'created_at' => '创建时间',
        ];
    }
}
