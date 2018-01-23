<?php


namespace backend\controllers;

use Yii;
use backend\models\search\LocationScheduleSearch;
use yii\web\Controller;

class DailyScheduleController extends \common\components\controllers\BaseController
{
    public function actionIndex($date)
    {
        $this->layout = 'base';
        $searchModel = new LocationScheduleSearch();
        $searchModel->date = $date;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
