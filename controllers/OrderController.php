<?php

namespace app\controllers;

use app\models\User;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\common\ExcelHelper;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    // ueditor 的图片访问路径前缀 （如 http://www.baidu.com）
//                  'imageUrlPrefix' => 'zmm.doo.com:8080'

                    // ueditor 的图片存储路径（示例中的路径会导致无法访问，因为通过url不可访问根目录上层的文件）
//                    "imagePathFormat"         => "/../runtime/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}",

                    // ueditor 图片上传的根目录(也可以通过这种方式更改图片上传的路径)
//                    'imageRoot' => Yii::getAlias('@webroot') . '/../runtime'
                ]
            ]
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExport()
    {
        $system_group = [
            'BUY系统'	=>'B2B组',
            'DMS系统'	=>'B2B组',
            '直供平台'	=>'B2B组',
            '订单中心'	=>'订单中心',
            'POS系统'	=>'门店组',
            '其他'		=>'其他',
            '发票系统'	=>'订单中心',
            'XDATA'		=>'XDATA',
            '海外商城'	=>'海外商城'
        ];
        // 如果没有规定开始结束日期则时间范围默认为上一周
        $start_day = !empty(Yii::$app->request->get('start_at')) ? str_replace('-','', Yii::$app->request->get('start_at')) : date('Ymd', strtotime('-' . (6+date('w')) . ' days'));
        $end_day = !empty(Yii::$app->request->get('end_at')) ? str_replace('-', '', Yii::$app->request->get('end_at')) : date('Ymd', strtotime('-' . (date('w')-1) . ' days'));

        $sql = "SELECT o.order_sn, u.user_name, IF(o.`status` = 20, '已完成', IF(o.`status`=10, '处理中', '待处理')) AS order_status, FROM_UNIXTIME(o.present_time) add_time, FROM_UNIXTIME(o.update_time) update_time, 
s.`name` AS system, CASE o.classify WHEN 1 THEN '用户操作问题' WHEN 2 THEN '系统Bug' WHEN 3 THEN '新需求' WHEN 4 THEN '导入导出/帮助类' WHEN 5 THEN '遗留/需排期' ELSE '待确定' END AS 问题分类, o.title, o.content, o.remark
FROM xm_order o
LEFT JOIN xm_system s ON s.id = o.system
LEFT JOIN xm_user u ON u.id = o.present_user
WHERE 1
AND o.is_del = 0
AND ((o.present_time > UNIX_TIMESTAMP($start_day) AND o.present_time < UNIX_TIMESTAMP($end_day))
OR (o.present_time > UNIX_TIMESTAMP($start_day) AND o.present_time < UNIX_TIMESTAMP($end_day))
OR (o.present_time > UNIX_TIMESTAMP($start_day) AND o.present_time < UNIX_TIMESTAMP($end_day)))
;";

        $ret = Yii::$app->db->createCommand($sql)->queryAll();
//        echo Yii::$app->db->createCommand($sql)->getRawSql();

        $header = [
            ['field' => 'order_sn',    'title' => 'order_sn', 'type' => 'string'],
            ['field' => 'user_name',    'title' => '创建人', 'type' => 'string'],
            ['field' => 'order_status',     'title' => '状态', 'type' => 'string'],
            ['field' => 'add_time',     'title' => '创建时间', 'type' => 'string'],
            ['field' => 'update_time',     'title' => '最后更新时间', 'type' => 'string'],
            ['field' => 'system',     'title' => '系统', 'type' => 'string'],
            ['field' => '问题分类',     'title' => '问题分类', 'type' => 'string'],
            ['field' => 'title',     'title' => '标题', 'type' => 'string'],
            ['field' => 'content',     'title' => '内容', 'type' => 'string'],
            ['field' => 'remark',     'title' => '原因&处理', 'type' => 'string'],
        ];

        $file_name = '工单' . date('Ymd') . '.csv';
        $dir = null; # 直接在页面下载

        if (!empty($ret)) {
            foreach ($ret as $k => $v) {
                $ret[$k]['content'] = strip_tags($v['content']);
                @$ret[$k]['system'] = $system_group[$v['system']];
            }

            ExcelHelper::export2DArrayByCSV($ret, $header, $file_name, $dir, true, $append = true);
            exit(); // 不打断点会报 headers already sent 错误
        } else {
            echo $sql . "<br/>";
            var_dump($ret);
            die();
        }
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 答复工单
     */
    public function actionReply()
    {
        $mail= Yii::$app->mailer->compose();
        $mail->setTo('704369798@qq.com'); //要发送给那个人的邮箱
        $mail->setSubject("DooTest"); //邮件主题
//        $mail->setTextBody('测试text'); //发布纯文字文本 //无法发送
        $mail->setHtmlBody("测试html"); //发送的消息内容
        var_dump($mail->send());
    }

    /**
     * 完成工单
     */
    public function actionSolve($id)
    {
        $model = $this->findModel($id);

        $model->scenario = 'solve'; //验证场景

        if ($model->load(Yii::$app->request->post()) && $model->save()){
            $model->trigger(Order::EVENT_AFTER_SOLVE); // 绑定事件
            return $this->redirect(['index']);
        } else {
            return $this->render('solve', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_del = 1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTest()
    {
        /*$present_users = [3,6,12];
        $info = (new Query())->from('xm_order')->where(['present_user' => $present_users])->indexBy('present_user')->all();

        $sql = (new Query())->from('xm_order')->where(['present_user' => $present_users])->indexBy('present_user')->createCommand()->getRawSql();

        echo $sql;*/

        if (PHP_SAPI != 'cli' && isset(Yii::$app->user)) {
            if (isset(Yii::$app->user->id)) {
                $user_id = Yii::$app->user->id;
            }

            if (isset(Yii::$app->user->identity)) {
                $user_name = Yii::$app->user->identity->username;
            }
        }

        var_dump($user_name);
    }
}
