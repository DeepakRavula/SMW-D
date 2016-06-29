<?php

namespace backend\controllers;

use Yii;
use common\models\Enrolment;
use common\models\Qualification;
use backend\models\EnrolmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EnrolmentController implements the CRUD actions for Enrolment model.
 */
class EnrolmentController extends Controller
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
        ];
    }

    /**
     * Lists all Enrolment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EnrolmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Enrolment model.
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
     * Creates a new Enrolment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Enrolment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Enrolment model.
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
     * Deletes an existing Enrolment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$enrolment = $this->findModel($id);
		$student_id = $enrolment->student_id;
        $enrolment->delete();
		Yii::$app->session->setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Program has been deleted successfully'
        ]);
        return $this->redirect(['student/view','id' => $student_id]);
    }

    /**
     * Finds the Enrolment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Enrolment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Enrolment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionTeachers() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$session = Yii::$app->session;
		$location_id = $session->get('location_id');
		$programId = $_POST['depdrop_parents'][0];
		$qualifications = Qualification::find()
					->joinWith(['teacher' => function($query) use($location_id) {
						$query->joinWith(['userLocation' => function($query) use($location_id){
							$query->joinWith('teacherAvailability')
						->where(['location_id' => $location_id]);
						}]);
					}])
					->where(['program_id' => $programId])
					->all();
		$result = [];
		$output = [];
		foreach($qualifications as  $qualification) {
			$output[] = [
				'id' => $qualification->teacher->id,
				'name' => $qualification->teacher->publicIdentity,
			];
		}
		$result = [
			'output' => $output,	
			'selected' => '',
		];
		
		return $result;
	}
}
