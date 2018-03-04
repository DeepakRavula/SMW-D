<?php

namespace backend\controllers;

use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use common\models\log\LogHistory;
use backend\models\search\TimelineEventSearch;

/**
 * CalendarEventColorController implements the CRUD actions for CalendarEventColor model.
 */
class TimelineEventController extends BaseController
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
                        'actions' => ['index',],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Creates a new CalendarEventColor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionIndex()
    {
        $logs = LogHistory::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $logs,
        ]);
	$searchModel=new TimelineEventSearch();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
	    'searchModel' =>$searchModel,
        ]);
    }
}
