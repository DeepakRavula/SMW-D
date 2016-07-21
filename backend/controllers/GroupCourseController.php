<?php

namespace backend\controllers;

use Yii;
use common\models\GroupCourse;
use common\models\GroupLesson;
use common\models\GroupEnrolment;
use common\models\User;
use common\models\Student;
use yii\data\ActiveDataProvider;
use backend\models\search\GroupCourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
/**
 * GroupCourseController implements the CRUD actions for GroupCourse model.
 */
class GroupCourseController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all GroupCourse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupCourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupCourse model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$location_id = Yii::$app->session->get('location_id');
		$query = GroupLesson::find()
				->joinWith('groupCourse')
				->where(['location_id' => $location_id,'course_id' => $id]);				
			$lessonDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		GroupEnrolment::deleteAll(['course_id' => $id]);
		
		$request = Yii::$app->request;
		$groupEnrolment = $request->post('GroupEnrolment');
		$studentIds = $groupEnrolment['studentIds']; 
		if( ! empty($studentIds)){	
			foreach($studentIds as $studentId){
				$groupEnrolment = new GroupEnrolment();
				$groupEnrolment->setAttributes([
					'course_id'	 => $id,
					'student_id' => $studentId,
				]);
				$groupEnrolment->save();
			} 
		}
	 
        return $this->render('view', [
            'model' => $this->findModel($id),
			'lessonDataProvider' => $lessonDataProvider,
        ]);
    }

    /**
     * Creates a new GroupCourse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GroupCourse();
		$teacherModel = ArrayHelper::map(User::find()
					->joinWith('userLocation ul')
					->join('INNER JOIN','rbac_auth_assignment raa','raa.user_id = user.id')
					->where(['raa.item_name' => 'teacher'])
					->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
					->all(),
				'id','userProfile.fullName'		
			);
		$model->location_id = Yii::$app->session->get('location_id');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Updates an existing GroupCourse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$teacherModel = ArrayHelper::map(User::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN','rbac_auth_assignment raa','raa.user_id = user.id')
			->where(['raa.item_name' => 'teacher'])
			->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
			->all(),
			'id','userProfile.fullName'		
		);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Deletes an existing GroupCourse model.
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
     * Finds the GroupCourse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GroupCourse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GroupCourse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
