<?php

namespace backend\controllers;

use Yii;
use common\components\controllers\BaseController;
use yii\filters\VerbFilter;
use backend\models\search\UserPinSearch;
use common\models\UserPin;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class UserPinController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ]
        ];
    }

    /**
     * Lists all Note models.
     * @return mixed
     */
    public function actionIndex()
    {
	$searchModel = new UserPinSearch();
        $request = Yii::$app->request;
        $dataProvider = $searchModel->search($request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdate($id)
    {
        $userPin = UserPin::findOne($id);
        if ($userPin->load(Yii::$app->request->post()) && $userPin->save()) {
            return [
                'status' => true,
                'message' => 'Pin updated successfully!'
            ];
        }
        $data = $this->renderAjax('_form', [
            'model' => $userPin,
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
