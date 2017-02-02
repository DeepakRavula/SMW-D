<?php

namespace backend\controllers;

use Yii;
use common\models\Course;
use common\models\Lesson;
use common\models\Qualification;
use backend\models\search\CourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Student;
use common\models\Enrolment;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use yii\web\Response;
use common\models\TeacherAvailability;
use common\models\PaymentFrequency;
/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseController extends Controller
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
				'only' => ['fetch-teacher-availability'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Course models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Course model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $enrolment = $request->post('Enrolment');
        $studentIds = $enrolment['studentIds'];
        if (!empty($studentIds)) {
            Enrolment::deleteAll(['courseId' => $id]);
            foreach ($studentIds as $studentId) {
                $enrolment = new Enrolment();
                $enrolment->setAttributes([
                    'courseId' => $id,
                    'studentId' => $studentId,
                    'isDeleted' => false,
                    'isConfirmed' => true,
                    'paymentFrequencyId' => PaymentFrequency::PAYMENT_FREQUENCY_FULL,
                ]);
                $enrolment->save();
            }
        }

        $studentDataProvider = new ActiveDataProvider([
            'query' => Student::find()
                ->groupCourseEnrolled($id)
				->active(),
        ]);

		$lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
				->andWhere([
					'courseId' => $id,
					'status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_SCHEDULED]
				])
				->notDeleted(),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'courseId' => $id,
            'studentDataProvider' => $studentDataProvider,
            'lessonDataProvider' => $lessonDataProvider,
        ]);
    }

    public function actionViewStudent($groupCourseId, $studentId)
    {
        $model = $this->findModel($groupCourseId);
        $studentModel = Student::findOne(['id' => $studentId]);

        return $this->render('view_student', [
            'model' => $model,
            'studentModel' => $studentModel,
        ]);
    }

	public function actionFetchTeacherAvailability($teacherId)
    {
		$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $teacherId]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$data = $this->renderAjax('_teacher-availability', [
        	'teacherDataProvider' => $teacherDataProvider,
    	]);

        return $data;
    }
    /**
     * Creates a new Course model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Course();
		$model->setScenario(Course::SCENARIO_GROUP_COURSE);
        $teacherModel = ArrayHelper::map(User::find()
                    ->joinWith(['userLocation ul' => function ($query) {
                        $query->joinWith('teacherAvailability');
                    }])
                    ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                    ->where(['raa.item_name' => 'teacher'])
                    ->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
                    ->all(),
                'id', 'userProfile.fullName'
            );
        $model->locationId = Yii::$app->session->get('location_id');
        if ($model->load(Yii::$app->request->post())) {
			if(Yii::$app->request->isAjax) {
				$response = Yii::$app->response;
		        $response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			} else {
				$model->save();
            	return $this->redirect(['lesson/review', 'courseId' => $model->id]);
			}
        } else {
            return $this->render('create', [
                'model' => $model,
                'teacher' => $teacherModel,
            ]);
        }
    }

    /**
     * Updates an existing Course model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $teacherModel = ArrayHelper::map(User::find()
                    ->joinWith('userLocation ul')
                    ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                    ->where(['raa.item_name' => 'teacher'])
                    ->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
                    ->all(),
                'id', 'userProfile.fullName'
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
     * Deletes an existing Course model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Course model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Course the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTeachers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $session = Yii::$app->session;
        $location_id = $session->get('location_id');
        $programId = $_POST['depdrop_parents'][0];
        $qualifications = Qualification::find()
                    ->joinWith(['teacher' => function ($query) use ($location_id) {
                        $query->joinWith(['userLocation' => function ($query) use ($location_id) {
                            $query->joinWith('teacherAvailability')
                        ->where(['location_id' => $location_id]);
                        }]);
                    }])
                    ->where(['program_id' => $programId])
                    ->all();
        $result = [];
        $output = [];
        foreach ($qualifications as  $qualification) {
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

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->where(['courseId' => $model->id])
                ->orderBy(['lesson.date' => SORT_ASC]),
        ]);

        $this->layout = '/print';

        return $this->render('_print', [
                    'model' => $model,
                    'lessonDataProvider' => $lessonDataProvider,
        ]);
    }
}
