<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
use backend\models\search\StudentBirthdaySearch;
/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentBirthdayController extends Controller
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

    /**
     * Displays a single Student model.
     *
     * @param int $id
     *
     * @return mixed
     */
    }
