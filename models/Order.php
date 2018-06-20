<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2017/11/17
 * Time: 12:21
 */

namespace app\models;

use app\base_models\Order as FaOrder;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

class Order extends FaOrder
{
    const EVENT_AFTER_SOLVE = 'event_after_solve';

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_SOLVE,[$this, 'afterSolve']);
//        $this->on(self::EVENT_AFTER_SOLVE,[$this, 'afterSolve2']);
//        $this->on(self::EVENT_AFTER_SOLVE,[$this, 'afterSolve3']); (可绑定多个方法)
    }

    public function rules()
    {
        return [
            [['present_user', 'system', 'level', 'title', 'content' ], 'required'],
            [['solve_user', 'classify'], 'required', 'on'=>'solve'], //只在solve场景下验证
            [['present_user', 'present_time', 'system', 'level', 'status', 'solve_user', 'solve_time', 'classify'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['order_sn'], 'string', 'max' => 30],
            [['order_sn'], 'unique', 'message' => '不要重复提交'],
            [['remark'], 'string', 'max' => 2000],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['present_time'], //自动添加‘发起时间’
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($this->isNewRecord){
                $this->order_sn = $this->generateOrderSn();
            }
            return true;
        }else{
            return false;
        }
    }

    public function afterSolve()
    {
        $this->solve_time = time();
        $this->status = 1;
        $this->save();
    }

    /**
     * 生成工单号
     * @return string
     */
    public function generateOrderSn()
    {
        $sn = 'WO' . str_pad(Yii::$app->user->id, 4, '0', STR_PAD_LEFT) . date('ymdHis');
        return $sn;
    }

    public function getPresenter()
    {
        return $this->hasOne(User::className(), ['id' => 'present_user']);
    }

    public function getSolver()
    {
        return $this->hasOne(User::className(), ['id' => 'solve_user']);
    }

    public static function statusList()
    {
        return [
            0 => '未解决',
            1 => '已解决',
        ];
    }


}