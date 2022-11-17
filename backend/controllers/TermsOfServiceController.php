<?php

namespace backend\controllers;

use Yii;
use common\models\Blog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class TermsOfServiceController extends \common\components\controllers\BaseController
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
                        'roles' => ['manageBlogs'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Blog models.
     *
     * @return mixed
     */
   public function actionIndex()
    {
       
        return $this->render('index');
    }
}
