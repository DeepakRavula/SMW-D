<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use common\models\Invoice;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class LessonController extends Controller
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
     * Lists all Lesson models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Lesson::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lesson model.
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
     * Creates a new Lesson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lesson();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Lesson model.
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
     * Deletes an existing Lesson model.
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
     * Finds the Lesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lesson the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lesson::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionInvoice($id) {
		$invoice = new Invoice();
		$model = Lesson::findOne(['id' => $id]);
		$duration = date("H:i",strtotime($model->enrolmentScheduleDay->duration));
		$unit = null;
		switch($duration){
			case '00:30':
				$unit = '0.5';
			break;
			case '00:45':
				$unit = '0.75';
			break;
			case '01:00':
				$unit = '1';
			break;
			case '01:30':
				$unit = '1.5';
			break;
		}
		$rate = $model->enrolmentScheduleDay->enrolment->qualification->program->rate;
		$tax = $rate * (13.5 / 100);
		$amount = $unit * $rate;
		$date = new \DateTime();
		$date->add(\DateInterval::createFromDateString('today'));
		$today = $date->format('Y-m-d');
		$invoice->setAttributes([
			'lesson_id' => $id,
			'unit' => $unit,
			'tax' =>  $tax,
			'subtotal'	 => $amount,
			'total' => $amount + $tax,
			'date' => $today,
			'status' => Invoice::STATUS_UNPAID,
		])	;
		$invoice->save();
	}
}
