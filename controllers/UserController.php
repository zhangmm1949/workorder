<?php

namespace app\controllers;

use app\models\PasswordForm;
use app\models\RegisterForm;
use app\models\UserSystem;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RegisterForm();
        $model->setScenario('user_create'); // 验证场景
//        var_dump(Yii::$app->request->post());die;

        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->systems = array_keys(UserSystem::getSystemsByUser($model->id));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model. （ 不允许删除用户 zmm 2019-09-12）
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == 1 ? 0 : 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @return User|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (!Yii::$app->user->identity->isAdmin){
            throw new ForbiddenHttpException('只有管理组成员才可以操作用户信息');
        }
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionRevisePassword(int $id)
    {
        if ($id <> Yii::$app->user->id){
            throw new ForbiddenHttpException('请不要尝试修改其他人的密码');
        }
        $model = new PasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->revise()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('password', [
                'model' => $model,
                'id' => $id,
            ]);
        }

    }

    public function actionTest(){
        $sys = UserSystem::getSystemsByUser(1);
        var_dump($sys);
    }

    public function actionGetUserIdsBySystem(int $system_id)
    {
        $data = UserSystem::getUsersBySystem($system_id);
        return json_encode(array_keys($data), JSON_UNESCAPED_UNICODE);
    }

    public function actionResetPassword()
    {
        $user_id = Yii::$app->request->get('id');
        $user = $this->findModel($user_id);
        $new_pass_str = '123456';
        $user->setPassword($new_pass_str);
        $user->generateAuthKey();
        $ret = $user->save(false);
        var_dump($ret);
    }
}
