<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use common\components\controllers\BaseController;
use common\components\rbac\Item;
use yii\filters\AccessControl;
/**
 * NoteController implements the CRUD actions for Note model.
 */
class PermissionController extends BaseController
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
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'add', 'remove'],
                        'roles' => ['managePrivileges'],
                    ],
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
        $permissions = Yii::$app->authManager->getLocationSpecificItems(Item::TYPE_PERMISSION);
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
