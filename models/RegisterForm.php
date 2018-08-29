<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2017/11/18
 * Time: 18:16
 */

namespace app\models;


use yii\base\Model;

class RegisterForm extends Model
{
    public $user_name;
    public $email;
    public $password;
    public $re_password;
    public $created_at;

    public function rules()
    {
        return [
            ['user_name', 'filter', 'filter' => 'trim'],
            ['user_name', 'required', 'message' => '用户名不能为空'],
            ['user_name', 'unique', 'targetClass' => 'app\models\User', 'message' => '用户名已存在.'],
            ['user_name', 'string', 'min' => 2, 'max' => 30],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => '邮箱不能为空'],
            ['email', 'email'],
            ['email', 'string', 'max'=>60],
            ['email', 'unique', 'targetClass'=>'app\models\User', 'message' => '此邮箱已被注册'],

            ['password', 'required', 'message' => '密码不能为空'],
            ['password', 'string', 'min' => 6, 'message' => '密码长度不能低于6位'],
            ['password', 'string', 'max' => 20, 'message' => '密码长度不超过20位'],

            ['re_password', 'compare', 'compareAttribute'=>'password','message'=>'两次密码不一致'],

            ['created_at', 'default', 'value' => time()],
        ];
    }

    /**
     * Register user
     *
     * @return bool 添加成功或者添加失败
     */
    public function register()
    {
        // 调用validate方法对表单数据进行验证，验证规则参考上面的rules方法
        if (!$this->validate()) {
            return null;
        }
        // 实现数据入库操作
        $user = new User();
        $user->user_name = $this->user_name;
        $user->email = $this->email;
        $user->created_at = $this->created_at;

        $user->setPassword($this->password);
        // 生成 "remember me" 认证key
        $user->generateAuthKey();

        // save(false)，不调用User的rules再做校验
        return $user->save(false);
    }

}