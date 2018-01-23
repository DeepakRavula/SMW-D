<?php


namespace frontend\controllers;

use Yii;
use frontend\models\search\LocationScheduleSearch;
use yii\web\Controller;

class DailyScheduleController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new LocationScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
            ]);
    }
}
