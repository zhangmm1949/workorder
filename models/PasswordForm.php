<?php
/**
 * Created by PhpStorm.
 * User: imor
 * Date: 18-8-28
 * Time: 下午7:57
 */

namespace app\models;


use yii\base\ErrorException;
use yii\base\Model;

class PasswordForm extends Model
{
    public $user_id;
    public $password;
    public $new_password;
    public $re_new_password;

    public function rules()
    {
        return [
            ['user_id', 'checkUserId', 'message' => '原密码不能为空'],
            ['password', 'required', 'message' => '原密码不能为空'],

            ['new_password', 'required', 'message' => '密码不能为空'],
            ['new_password', 'string', 'min' => 6, 'message' => '密码长度不能低于6位'],
            ['new_password', 'string', 'max' => 20, 'message' => '密码长度不超过20位'],

            ['re_new_password', 'compare', 'compareAttribute'=>'new_password','message'=>'两次密码不一致'],
        ];
    }

    public function checkUserId()
    {
        if ($this->user_id <> \Yii::$app->user->id){
            $this->addError('user_id', '请勿尝试修改其他用户的密码');
        }
    }

    public function revise()
    {
        // 调用validate方法对表单数据进行验证，验证规则参考上面的rules方法
        if (!$this->validate()) {
            return null;
        }
        // 实现数据入库操作
        $user = User::findIdentity($this->user_id);

        if (!$user->validatePassword($this->password)){
            $this->addError('password', '原密码错误');
            return false;
        }

        $user->setPassword($this->new_password);

        // save(false)，不调用User的rules再做校验
        return $user->save(false);
    }

}