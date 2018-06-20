<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
    public $start_at;
    public $end_at;

    public $user_name;
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()->orderBy('present_time DESC');

        if (!Yii::$app->user->identity->isAdmin){
            $query -> andFilterWhere(['=', 'present_user', Yii::$app->user->id]);
        }

        $query->joinWith(['presenter']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
//             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'present_user' => $this->present_user,
            'solve_user' => $this->solve_user,
            'system' => $this->system,
            'order.status' => $this->status,
            'order.classify' => $this->classify,
        ]);

        if ($this->start_at){
            $this->start_at = strtotime($this->start_at);
            $query->andFilterWhere(['>=', 'present_time', $this->start_at]);
        }

        if ($this->end_at){
            $this->end_at = strtotime("$this->end_at + 1 day");
            $query->andFilterWhere(['<=', 'present_time', $this->end_at]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'presenter.user_name', $this->user_name])
            ->andFilterWhere(['like', 'solver.user_name', $this->user_name]);

        return $dataProvider;
    }
}
