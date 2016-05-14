<?php
namespace backend\models;

use common\models\User;
use common\models\UserProfile;
use common\models\PhoneLabel;
use common\models\PhoneNumber;
use common\models\Program;
use common\models\Qualification;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
	public $qualifications;
    public $lastname;
    public $firstname;
	public $phonenumber;
	public $phonelabel;
	public $phoneextension;
    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'unique', 'targetClass' => User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=> User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],

            [['status'], 'integer'],
            [['qualifications'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
					Program::find()->active()->all(),	
                    'id'
                )]
            ],
            [['roles'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->authManager->getRoles(),
                    'name'
                )]
            ],
            
            ['lastname', 'filter', 'filter' => 'trim'],
            ['lastname', 'required'],
            ['lastname', 'string', 'min' => 2, 'max' => 255],
            
            ['firstname', 'filter', 'filter' => 'trim'],
            ['firstname', 'required'],
            ['firstname', 'string', 'min' => 2, 'max' => 255],

			['phonelabel','required'],
			['phoneextension','integer'],
			['phonenumber','required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'Email'),
            'status' => Yii::t('common', 'Status'),
            'password' => Yii::t('common', 'Password'),
            'roles' => Yii::t('common', 'Roles'),
            'lastname' => Yii::t('common', 'Last Name'),
            'firstname' => Yii::t('common', 'First Name'),
            'phonelabel' => Yii::t('common', 'Phone Label'),
            'phonenumber' => Yii::t('common', 'Phone Number'),
            'phoneextension' => Yii::t('common', 'Phone Extension')
        ];
    }

    /**
     * @param User $model
     * @return mixed
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );
       	$userFirstName = UserProfile::findOne(['user_id' => $model->getId()]); 
	  		if(! empty($userFirstName->firstname)){
				$this->firstname = $userFirstName->firstname;
	   	}
	    $userLastName = UserProfile::findOne(['user_id' => $model->getId()]); 
	   		if(! empty($userLastName->lastname)){
			   $this->lastname = $userLastName->lastname;
	   	}
	   	
		$phoneNumber = PhoneNumber::findOne(['user_id' => $model->getId()]); 
	   		if(! empty($phoneNumber->number) || ! empty($phoneNumber->extension)){
			   $this->phoneextension = $phoneNumber->extension;
			   $this->phonenumber = $phoneNumber->number;
			   
	   	} 
		
		$this->phonelabel = ArrayHelper::getColumn(
	        PhoneLabel::findByPhoneLabel($model->getId()), 'name'		
        );
		$this->qualifications = ArrayHelper::getColumn(
				Program::find()->active()->all(), 'id'
		);
        return $this->model;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }
        return $this->model;
    }

    /**
     * Signs user up.
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            $model->username = $this->username;
            $model->email = $this->email;
            $model->status = $this->status;
            if ($this->password) {
                $model->setPassword($this->password);
            }
            $lastname = $this->lastname;
            $firstname = $this->firstname;
			$phonenumber = $this->phonenumber;
			$phoneextension = $this->phoneextension;
			$phonelabel = $this->phonelabel;
			$model->location_id = Yii::$app->session->get('location_id');
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }
            if ($isNewRecord) {
                $model->afterSignup();
            }
            
            
            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if ($this->roles && is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $auth->assign($auth->getRole($role), $model->getId());
                }
            }

			if (current(Yii::$app->authManager->getRolesByUser($model->getId()))->name === User::ROLE_TEACHER) {
				Qualification::deleteAll(['teacher_id' => $model->getId()]);
				if ($this->qualifications && is_array($this->qualifications)) {
					foreach ($this->qualifications as $qualification) {
						$qualificationModel = new Qualification();
						$qualificationModel->program_id = $qualification;
						$qualificationModel->teacher_id = $model->getId();
						$qualificationModel->save();
					}
				}
					
			}
            $userProfileModel = UserProfile::findOne($model->getId());
            $userProfileModel->lastname = $lastname;
            $userProfileModel->firstname = $firstname;
            $userProfileModel->save();
            //$model->link('userProfile', $userProfileModel); //automatically saved into database
			
			$phoneNumberModel = PhoneNumber::findOne($model->getId());
			if(empty($phoneNumberModel)){
				$phoneNumberModel = new PhoneNumber();
				$phoneNumberModel->user_id = $model->getId();
			}
            $phoneNumberModel->extension = $phoneextension;
            $phoneNumberModel->number = $phonenumber;
            $phoneNumberModel->label_id = $phonelabel;
            $phoneNumberModel->save();
		
            return !$model->hasErrors();
        }
        return null;
    }
}
