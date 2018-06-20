<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2017/11/8
 * Time: 18:00
 */

namespace app\models;

use app\base_models\User as Fa_User;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use Yii;

class User extends Fa_User implements IdentityInterface
{
    // 用户可用
    const STATUS_ENABLE = 1;
    // 用户不可用
    const STATUS_DISABLE = 2;

    public $_isAdmin;

    public $admin_ids = [1];


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /*foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;*/

        // 数据库中去掉了accessToken 字段
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }


    /**
     * Finds user by name
     *
     * @param  string      $name
     * @return mixed  User Model|null
     */
    public static function findByUsername($name)
    {
        return static::find()->where('(email=:email OR user_name=:name) AND status=:status', [
            ':email' => $name,
            ':name' => $name,
            ':status' => self::STATUS_ENABLE,
        ])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function getUserList()
    {
        $data = self::find()->asArray()->all();
        return ArrayHelper::map($data, 'id', 'user_name');
    }

    public function getIsAdmin()
    {
        return $this->_isAdmin = in_array($this->id, $this->admin_ids);
    }

}