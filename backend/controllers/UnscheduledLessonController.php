<?php

namespace backend\controllers;

use Yii;
use backend\models\SystemLog;
use backend\models\search\SystemLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\search\UnscheduledLessonSearch;

/**
 * LogController implements the CRUD actions for SystemLog model.
 */
class UnscheduledLessonController extends \common\components\controllers\BaseController
{
    

    /**
     * Lists all SystemLog models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnscheduledLessonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
