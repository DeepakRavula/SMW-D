<?php

namespace backend\controllers;

use Yii;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use common\models\Location;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * LessonController implements the CRUD actions for Lesson model.
 */
class ExplodedLessonController extends BaseController
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
            // 'contentNegotiator' => [
            //     'class' => ContentNegotiator::className(),
            //     'only' => [],
            //     'formatParam' => '_format',
            //     'formats' => [
            //        'application/json' => Response::FORMAT_JSON,
            //     ],
            // ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index'
                        ],
                        'roles' => ['managePrivateLessons', 
							'manageGroupLessons'],
                    ],
                ],
            ],  
        ];
    }

    /**
     * Lists all Lesson models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $explodedLessons = Lesson::find()
            ->notDeleted()
            ->privateLessons()
            ->isConfirmed()
            ->location($locationId)
            ->split()
            ->notCanceled();
        $dataProvider = new ActiveDataProvider([
            'query' => $explodedLessons,
            'pagination' => false
        ]);
              
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
