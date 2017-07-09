<?php


namespace frontend\controllers;

use Yii;
use frontend\models\search\TeacherScheduleSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class DailyScheduleController extends Controller
{
 
    public function actionIndex()
    {
		$searchModel = new TeacherScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        	return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        	]);
    }
}
