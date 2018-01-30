<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use backend\models\search\StudentBirthdaySearch;
use common\models\Location;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentBirthdayController extends BaseController
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
                'only' => ['create', 'update'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'print'],
                        'roles' => ['manageBirthdays'],
                    ],
                ],
            ], 
        ];
    }

    /**
     * Lists all Student models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentBirthdaySearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('d-m-Y');
        $nextSevenDate = $currentDate->modify('+7days');
        $searchModel->toDate = $nextSevenDate->format('d-m-Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $studentBirthdayRequest = $request->get('StudentBirthdaySearch');
            $searchModel->dateRange = $studentBirthdayRequest['dateRange'];
        }
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    public function actionPrint()
    {
        $searchModel = new StudentBirthdaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Location::findOne(['id' => $locationId]);
        $this->layout = '/print';

        return $this->render('/report/student-birthday/_print', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model'=>$model,
        ]);
    }
}
