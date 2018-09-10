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

    public  $systems;
    private $isAdmin;
    public  $admin_ids = [1, 47, 49, 52];// 张萌萌 杨恩 彭太升 管东岳
    private $isSuperAdmin;
    public  $superAdmin = [1];


    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT,[UserSystem::class, 'updateUserSystems'],'insert');
        $this->on(self::EVENT_AFTER_UPDATE,[UserSystem::class, 'updateUserSystems'],'update');
        $this->on(self::EVENT_AFTER_DELETE,[UserSystem::class, 'updateUserSystems'],'delete');
    }

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
            [['systems'], 'required', 'message'=>'关联系统不能为空'],
        ];
    }


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
     * Finds user by name OR Email prefix
     * Email prefix 登录方式仅限小米公司邮箱用户使用
     *
     * @param  string      $name
     * @return mixed  User Model|null
     */
    public static function findByUsername($name)
    {
        return static::find()->where('(email=:email OR user_name=:name) AND status=:status', [
            ':email' => $name . '@xiaomi.com',
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

    public static function getUserList($dept_id=0)
    {
        $dept_id = $dept_id ? intval($dept_id) : 0;
        $data = self::find()->asArray()->where(['department_id'=>$dept_id, 'status'=>1])->all();
        return ArrayHelper::map($data, 'id', 'user_name');
    }

    public function getIsAdmin()
    {
        return $this->isAdmin = in_array($this->id, $this->admin_ids);
    }

    public function getIsSuperAdmin()
    {
        return $this->isSuperAdmin = in_array($this->id, $this->superAdmin);
    }

}