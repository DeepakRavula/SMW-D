<?php

namespace backend\controllers;

use Yii;
use common\models\CalendarEventColor;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
use yii\filters\AccessControl;

/**
 * CalendarEventColorController implements the CRUD actions for CalendarEventColor model.
 */
class CalendarEventColorController extends \common\components\controllers\BaseController
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
                        'actions' => ['edit'],
                        'roles' => ['manageColorCode'],
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
    public function actionEdit()
    {
        $eventModels = CalendarEventColor::find()->all();
        $request = Yii::$app->request;
        Model::loadMultiple($eventModels, $request->post());
        foreach ($eventModels as $eventModel) {
            $eventModel->save();
        }
        return $this->render('create', [
            'eventModels' => $eventModels,
        ]);
    }
}
