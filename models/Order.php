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

    private $remark_view;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_SOLVE,[$this, 'afterSolve']);

        $this->on(self::EVENT_AFTER_INSERT,[$this, 'updateOrderTag'], 'insert');
        $this->on(self::EVENT_AFTER_UPDATE,[$this, 'updateOrderTag'], 'update');
        $this->on(self::EVENT_AFTER_DELETE,[$this, 'updateOrderTag'], 'delete');
    }

    public function rules()
    {
        return [
            [['present_user', 'system', 'level', 'title', 'content'], 'required', 'message'=>'不能为空'],
            [['solve_user', 'classify'], 'required', 'on'=>'solve'], //只在solve场景下验证
            [['present_user', 'present_time', 'system', 'level', 'status', 'solve_user', 'solve_time', 'classify'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['order_sn'], 'string', 'max' => 30],
            [['order_sn'], 'unique', 'message' => '不要重复提交'],
            [['remark'], 'string', 'max' => 2000],
            [['tags'], 'string', 'max' => 50]
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['present_time'], //自动添加‘发起时间’
                ],
            ],
        ];
    }

    /**
     * 数据存入数据库之前的一些操作
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($this->isNewRecord){
                $this->order_sn = $this->generateOrderSn(); //生成订单sn
                $this->tags = $this->setTags($this->tags); //将表单提交来的tags格式化
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * 事件方法, “处理工单”(actionSolve) 操作后的处理。
     */
    public function afterSolve()
    {
        $this->solve_time = time();
        $this->status = 1;
        $this->save();
    }

    /**
     * @param $data object 事件绑定中传递的参数
     */
    public function updateOrderTag($data)
    {
        $tag = new Tag();
        $action = $data->data; //操作类型 update/insert/delete 在事件绑定中传递的参数

        if (!empty($this->tags)){
            $tag->updateTags($this, $action);
        }

    }

    /**
     * @param $tags string 表单提交的tags
     * @return string
     */
    public function setTags($tags)
    {
        $find = ['，', ',', '"', ' ', '/', '\\'];
        $replace = '|';
        // 可能提交的分隔符 全部替换为‘|’ ，然后打撒为数组去重，去空，重新拼接为字符串
        $tags_array = array_filter(array_unique(explode('|',str_replace($find,$replace,trim($tags)))));
        return $this->tags = implode('|',$tags_array);
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

    /**
     * 获取提出人的信息
     * @return \yii\db\ActiveQuery
     */
    public function getPresenter()
    {
        return $this->hasOne(User::class, ['id' => 'present_user']);
    }

    /**
     * 获取解决人的信息
     * @return \yii\db\ActiveQuery
     */
    public function getSolver()
    {
        return $this->hasOne(User::class, ['id' => 'solve_user']);
    }

    public static function statusList()
    {
        return [
            0 => '未解决',
            1 => '已解决',
        ];
    }

    /**
     * 首页显示的备注 截取100字符（太长会影响页面结构
     * @return string
     */
    public function getRemark_view()
    {
        return $this->remark_view = strlen($this->remark) > 100 ? mb_substr($this->remark, 0, 100) . '…' : $this->remark;
    }


}