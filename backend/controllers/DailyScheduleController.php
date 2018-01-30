<?php


namespace backend\controllers;

use Yii;
use backend\models\search\LocationScheduleSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;

class DailyScheduleController extends BaseController
{
	public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['manageSchedule'],
                    ],
                ],
            ],
        ];
    }
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
