<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Location;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use yii\helpers\Url;
use common\models\Holiday;
use common\models\TeacherAvailability;
use common\models\Classroom;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class Schedule2Controller extends Controller
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
                        'actions' => ['index'],
                        'roles' => ['staffmember'],
                    ],
                ],
            ],
            'contentNegotiator' => [
               'class' => ContentNegotiator::className(),
               'only' => ['day-event', 'classroom-event'],
               'formatParam' => '_format',
               'formats' => [
                   'application/json' => Response::FORMAT_JSON,
               ],
           ],
        ];
    }

    /**
     * Lists all Qualification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
		return $this->render('index');
    }
}