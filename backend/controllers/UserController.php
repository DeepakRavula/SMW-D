<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserLocation;
use common\models\UserProfile;
use common\models\UserAddress;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\TeacherAvailability;
use common\models\UserImport;
use backend\models\UserForm;
use backend\models\UserImportForm;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\Student;
use common\models\Program;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
        ];
    }


	public function actions() {
		return [
			'upload' => [
				'class' => 'trntv\filekit\actions\UploadAction',
				'multiple' => false,
				'disableCsrf' => true,
				'responseFormat' => yii\web\Response::FORMAT_JSON,
				'responsePathParam' => 'path',
				'responseBaseUrlParam' => 'base_url',
				'responseUrlParam' => 'url',
				'responseDeleteUrlParam' => 'delete_url',
				'responseMimeTypeParam' => 'type',
				'responseNameParam' => 'name',
				'responseSizeParam' => 'size',
				'deleteRoute' => 'delete',
				'fileStorage' => 'fileStorage', // Yii::$app->get('fileStorage')
				'fileStorageParam' => 'fileStorage', // ?fileStorage=someStorageComponent
				'sessionKey' => '_uploadedFiles',
				'allowChangeFilestorage' => false,
				'validationRules' => [
				],
				'on afterSave' => function($event) {

					/* @var $file \League\Flysystem\File */
					$file = $event->file;
					$userImport = new UserImport();
					$userImport->file = $file;
					$userImport->import();
					// do something (resize, add watermark etc)
				}
			]
		];
	}

	/**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$session = Yii::$app->session;
        Yii::$app->session->set("customer_id" , $id);
        $dataProvider = new ActiveDataProvider([
            'query' => Student::find()->where(['customer_id' => $id])
        ]);
 		$model = $this->findModel($id);
		
		$teacherAvailabilityModel = new TeacherAvailability();
		$teacherLocation = UserLocation::findOne([
			'user_id' => $id,
			'location_id' => $session->get('location_id'),
		]);
		$teacherAvailabilityModel->teacher_location_id = $teacherLocation->id;

		$dataProvider1 =  new ActiveDataProvider([
            'query' => TeacherAvailability::find()
				->where([
					'teacher_location_id' => $teacherLocation->id
				])
			]);

        if ($teacherAvailabilityModel->load(Yii::$app->request->post()) ) {

			$fromtime = date("H:i:s",  strtotime($_POST['TeacherAvailability']['from_time']));
			$totime = date("H:i:s", strtotime($_POST['TeacherAvailability']['to_time']));

			$teacherAvailabilityModel->from_time = $fromtime;
			$teacherAvailabilityModel->to_time = $totime;
			
			if($teacherAvailabilityModel->save()) {
            	return $this->redirect(['view', 'id' => $model->id]);
			}
        }
        return $this->render('view', [
            'student' => new Student(),
            'dataProvider' => $dataProvider,
			'dataProvider1' => $dataProvider1,
            'model' =>$model, 
            'teacherAvailabilityModel' => $teacherAvailabilityModel, 
        ]);
	}

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();
        $model->setScenario('create');
        $model->roles = Yii::$app->request->queryParams['User']['role_name'];
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'UserSearch[role_name]' => $model->roles]);
        }

        return $this->render('create', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
            'programs' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name')
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new UserForm();
        $model->setModel($this->findModel($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'UserSearch[role_name]' => $model->roles]);
        }

        return $this->render('update', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
            'programs' => ArrayHelper::map(Program::find()->active()->all(), 'id', 'name')
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionImport()
    {
        $model = new UserImportForm();
        if ($model->load(Yii::$app->request->post()) && $model->import()) {
            return $this->redirect(['import']);
        }

        return $this->render('import', [
			'model' => $model,
		]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $userLocationModel = UserLocation::findAll(["user_id"=>$id]);
        
        if(count($userLocationModel) == 1)
        {            
            Yii::$app->authManager->revokeAll($id);
            $this->findModel($id)->delete();
			$userLocationModel = UserLocation::findOne(["user_id"=>$id, "location_id"=>Yii::$app->session->get('location_id')]);
            $userLocationModel->delete();
        }else{
            $userLocationModel = UserLocation::findOne(["user_id"=>$id, "location_id"=>Yii::$app->session->get('location_id')]);
            $userLocationModel->delete();            
        }
        
        return $this->redirect(['index']);
    }


    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteAll()
    {
		$db = Yii::$app->db;
		$command = $db->createCommand("DELETE u, up, pn, ua, a,s,raa  FROM `user` u
			LEFT JOIN `user_profile` up ON u.`id` = up.`user_id`
			LEFT JOIN `phone_number` pn ON u.`id` = pn.`user_id`
			LEFT JOIN `user_address` ua ON u.`id` = ua.`user_id` 
			LEFT JOIN `student` s ON s.`customer_id` = u.`id`           
			LEFT JOIN `address` a ON a.`id` = ua.`address_id` 
			LEFT JOIN `rbac_auth_assignment` raa ON raa.`user_id` = u.`id`  
			WHERE raa.`item_name` = 'customer'");
		$command->execute();

		Yii::$app->session->setFlash('alert', [
			'options'=>['class'=>'alert-success'],
			'body'=>Yii::t('backend', 'All customer and student records have been deleted successfully ', [])
		]);
        return $this->redirect(['index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
