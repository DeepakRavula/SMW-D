<?php

namespace backend\controllers;

use Yii;
use common\models\Vacation;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\PaymentCycle;
use yii\web\Response;
/**
 * VacationController implements the CRUD actions for Vacation model.
 */
class VacationController extends Controller
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
				'only' => ['create', 'delete'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Vacation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Vacation::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vacation model.
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
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($enrolmentId)
    {
        $enrolment = Enrolment::findOne(['id' => $enrolmentId]);
        $dateRange = Yii::$app->request->get('dateRange');
        $creditAmount = 0;
        $lessonDataProvider = null;
        $paymentCycleDataProvider = null;
        if ($dateRange) {
            list($fromDate, $toDate) = explode(' - ', $dateRange);

            $startDate = \DateTime::createFromFormat('M d,Y', $fromDate)->format('Y-m-d');
            $endDate = \DateTime::createFromFormat('M d,Y', $toDate)->format('Y-m-d');
            $lessonsQuery   = Lesson::find()
                            ->notDeleted()
                            ->isConfirmed()
                            ->andWhere(['courseId' => $enrolment->courseId])
                            ->andWhere(['AND', ['>=', 'DATE(date)', $startDate], ['<=', 'DATE(date)', $endDate]]);
            $paymentCyclesQuery = PaymentCycle::find()
                    ->where(['enrolmentId' => $enrolmentId])
                    ->andWhere(['OR', ['between', "DATE(endDate)", $startDate, $endDate],
                                ['between', "DATE(startDate)", $startDate, $endDate]]);
            
            $affectedLessons = $lessonsQuery->all();
            $affectedLessonIds = [];
            foreach ($affectedLessons as $lesson) {
                if ($lesson->hasLessonCredit($enrolmentId)) {
                    $creditAmount += $lesson->getLessonCreditAmount($enrolmentId);
                }
                $affectedLessonIds[] = $lesson->id;
            }
            $paymentCycles = $paymentCyclesQuery->all();
            $affectedPaymentCycleIds = [];
            foreach ($paymentCycles as $paymentCycle) {
                foreach ($paymentCycle->lessons as $lesson) {
                    $lessonId = $lesson->id;
                    $isAffected = true;
                    if (!$lesson->isDeleted && !in_array($lessonId, $affectedLessonIds)) {
                        $isAffected = false;
                    }
                    if ($isAffected) {
                        $affectedPaymentCycleIds[] = $paymentCycle->id;
                    }
                }
            }
            $affectedPaymentCycles = PaymentCycle::find()
                    ->where(['id' => $affectedPaymentCycleIds]);
            if ($affectedLessons) {
                $lessonDataProvider = new ActiveDataProvider([
                    'query' => $lessonsQuery
                ]);
            }
            if ($affectedPaymentCycleIds) {
                $paymentCycleDataProvider = new ActiveDataProvider([
                    'query' => $affectedPaymentCycles
                ]);
            }
        }
        $data = $this->renderAjax('/student/vacation/_form', [
                'model' => new Vacation(),
                'enrolmentId' => $enrolmentId,
                'studentId' => $enrolment->studentId,
                'creditAmount' => $creditAmount,
                'lessonDataProvider' => $lessonDataProvider,
                'paymentCycleDataProvider' => $paymentCycleDataProvider
        ]);
		
        $request = Yii::$app->request;
        $model = new Vacation();
        if ($model->load($request->post())) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            Lesson::deleteAll([
                    'courseId' => $enrolment->course->id,
                    'isConfirmed' => false
            ]);
            $transaction->commit();
            $model->enrolmentId = $enrolmentId;
            if($model->save()) {
                return [
                    'status' => true
                ]; 
            } else {
                return [
                    'status' => false,
                    'errors'=>$model->getErrors($attribute='dateRange'),
                ]; 
               
            }
        } else {
            return [
                'status' => true,
                'data' => $data,
            ]; 
        }
    }

    /**
     * Updates an existing Vacation model.
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
     * Deletes an existing Vacation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		$model->delete();
		return [
			'status' => true,
		];
    }

    /**
     * Finds the Vacation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Vacation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
