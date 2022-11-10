<?php

namespace backend\controllers;

use Yii;
use common\models\Note;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class NoteController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['create', 'update'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'create'],
                        'roles' => ['manageStudents', 
							'manageCustomers', 'manageTeachers',
							'managePrivateLessons', 'manageGroupLessons', 'managePfi', 'manageInvoices', 'manageAdmin', 'manageStaff', 'manageOwners'],
                    ],
                ],
            ],  
        ];
    }

    /**
     * Lists all Note models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Note::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Note model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Note model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($instanceId, $instanceType)
    {
        $userId = Yii::$app->user->id;
        $model = new Note();
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            $model->instanceId = $instanceId;
            $model->instanceType = $instanceType;
            $model->createdUserId = $userId;
            if ($model->save()) {
                $notes = Note::find()
                        ->andWhere(['instanceId' => $model->instanceId, 'instanceType' => $model->instanceType]);
                $noteDataProvider = new ActiveDataProvider([
                        'query' => $notes,
                    ]);
                $view = '/' . $model->getInstanceTypeName() . '/note/_view';
                $data = $this->renderAjax($view, [
                        'noteDataProvider' => $noteDataProvider,
                        'model' => new Note()
                    ]);
                $response = [
                        'status' => true,
                        'data' => $data,
                    ];
            } else {
                $errors = ActiveForm::validate($model);
                $response = [
                    'status' => false,
                    'errors' =>  $errors
                ];
            }
            return $response;
        }
    }
    
    /**
     * Updates an existing Note model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->hasEditable && $model->save()) {
            return ['output' => $model->content, 'message' => ''];
        }
    }

    /**
     * Deletes an existing Note model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Note model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Note the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Note::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
