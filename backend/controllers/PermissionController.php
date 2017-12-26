<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class PermissionController extends Controller
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
				'only' => ['add', 'remove'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Note models.
     * @return mixed
     */
    public function actionIndex()
    {
		$permissions = Yii::$app->authManager->getPermissions();
		$roles = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'name');
        return $this->render('index', [
			'permissions' => $permissions,
			'roles' => $roles
        ]);
    }
	public function actionAdd($role, $permission)
    {
		$auth = Yii::$app->authManager;
        $role = $auth->getRole($role);
		$permission = $auth->getPermission($permission);
		$auth->addChild($role, $permission);
        return ['success'=>true];
    }
	public function actionRemove($role, $permission)
    {
      	$auth = Yii::$app->authManager;
        $role = $auth->getRole($role);
		$permission = $auth->getPermission($permission);
		$auth->removeChild($role, $permission);
        return ['success'=>true]; 
    }
}
