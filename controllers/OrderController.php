<?php

namespace app\controllers;

use app\models\common\PdfHelper;
use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

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
        $this->findModel($id)->delete();

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

    public function actionPdf($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);

        $html = $this->render('view', ['model' => $model]);

        $cssInline = <<<EOF
    html,body{
        padding: 0;
        margin: 0;
        font-size: 14px;
        color: rgba(0,0,0,0.8);
        font-family:Helvetica,Arial,Sans-serif;
    }
    table{
        width: 1080px;
        margin: 0 auto;
    }
    table td{
        text-align: center;
        min-height: 40px;
    }
    .info{
        margin-bottom: 10px;
        margin-top: 20px;
    }
    .info td{
        text-align: left;
        padding: 4px 0
    }
    .list td{
        border-top:2px solid #7e7e7e;
        border-right:2px solid #7e7e7e;
    }
    .list{
        border-left: 2px solid #7e7e7e;
        border-bottom: 2px solid #7e7e7e;
    }
    .border-td{
        padding:4px;
        width: 300px;
        border: 2px solid #7e7e7e;
    }
    .border-td.first{
        border-bottom:none;
    }
    .logo{
        float: left;
        height: 40px;
        padding-bottom: 10px;
    }
    .title{
        font-size: 24px;
        line-height: 40px;
    }
    .footer{
        margin-top: 20px;
        margin-bottom: 60px;
    }
    .footer td{
        line-height: 24px;
        position: relative;
    }
    .huizhang{
        position: absolute;
        top: -70px;
        right: 0;
        width: 180px;
    }
EOF;
        PdfHelper::setDownload();
        // setup kartik\mpdf\Pdf component
        $pdf = PdfHelper::createPdf($html, $cssInline, 'MI XiaoMi');

        $pdf->filename = date('Ymd'). 'Issue.pdf';
        return $pdf->render();




    }

    public function actionTest()
    {
        $present_users = [3,6,12];
        $info = (new Query())->from('xm_order')->where(['present_user' => $present_users])->indexBy('present_user')->all();

        $sql = (new Query())->from('xm_order')->where(['present_user' => $present_users])->indexBy('present_user')->createCommand()->getRawSql();

        echo $sql;

        var_dump($info);die;
    }
}
