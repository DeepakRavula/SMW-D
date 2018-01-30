<?php

namespace backend\controllers;

use Yii;
use common\models\TeacherRoom;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserForm;
use yii\base\Model;
use common\models\TeacherAvailability;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * TeacherRoomController implements the CRUD actions for TeacherRoom model.
 */
class TeacherRoomController extends BaseController
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
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'view', 'delete', 'create'],
                        'roles' => ['manageTeachers'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all TeacherRoom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TeacherRoom::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeacherRoom model.
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
     * Creates a new TeacherRoom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        TeacherRoom::deleteAll(['teacherId' => $id]);
        $request = Yii::$app->request;
        $models = UserForm::createMultiple(TeacherRoom::classname());
        Model::loadMultiple($models, $request->post());
        foreach ($models as $model) {
            $dayList = TeacherAvailability::getWeekdaysList();
            $day = array_search($model->day, $dayList);
            $model->day = $day;
            $model->save();
        }
        return $this->redirect(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $id, '#' => 'classroom']);
    }

    /**
     * Updates an existing TeacherRoom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Deletes an existing TeacherRoom model.
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
     * Finds the TeacherRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TeacherRoom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherRoom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
