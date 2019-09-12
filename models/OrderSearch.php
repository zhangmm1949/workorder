<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
    public $start_at;
    public $end_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['present_user', 'present_time', 'system', 'status', 'classify'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * ！！！ 用户部门决定操作权限，关联系统决定可见范围。 ！！！
     * 可见工单范围应以给用户分配的系统来限定，不应限定到个人。A 用户创建的工单，对 B 用户亦有参考价值
     *
     * @param array $params
     * @param bool $export
     *
     * @return ActiveDataProvider
     */
    public function search($params, $export=false)
    {
        $query = Order::find()->orderBy('status ASC, present_time DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        if ($this->start_at){
            $start_time = strtotime($this->start_at);
            $query->andFilterWhere(['>=', 'present_time', $start_time]);
        }

        if ($this->end_at){
            $end_time = $this->end_at . ' 23:59:59';
            $end_time = strtotime($end_time);
            $query->andFilterWhere(['<=', 'present_time', $end_time]);
        }

        if ($this->start_at && $this->end_at){
            $start_time = strtotime($this->start_at);
            $end_time = $this->end_at . ' 23:59:59';
            $end_time = strtotime($end_time);
            $query->where("((present_time > $start_time AND present_time < $end_time) OR (solve_time > $start_time AND solve_time < $end_time) OR (update_time > $start_time AND update_time < $end_time))");
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'present_user' => $this->present_user,
            'solve_user' => $this->solve_user,
            'system' => $this->system,
            'xm_order.status' => $this->status,
            'xm_order.classify' => $this->classify,
        ]);

        $query->andWhere(['is_del'=>0]); # 已删除订单不显示

        $query->andFilterWhere(['like', 'title', $this->title]);

        //只允许搜索自己可见系统内的工单
        $query -> andWhere(['in', 'system', UserSystem::getSystemIdsByUserId(Yii::$app->user->id)]);
        if ($export==true){
            $query->asArray();
        }

        return $dataProvider;
    }
}
