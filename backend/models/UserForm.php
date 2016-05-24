<?php
namespace backend\models;

use common\models\User;
use common\models\UserProfile;
use common\models\PhoneLabel;
use common\models\UserAddress;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\Program;
use common\models\Qualification;
use common\models\UserLocation;
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
	public $addresslabel;
	public $city;
	public $province;
	public $postalcode;
	public $country;
	public $address;
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
			['phonenumber','required'],
					
			['address','required'],
			['addresslabel','required'],
			['postalcode','required'],
			['province','required'],
			['city','required'],
			['country','required']
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
            'phoneextension' => Yii::t('common', 'Phone Extension'),
            'address' => Yii::t('common', 'Address'),
            'addresslabel' => Yii::t('common', 'Address Label'),
            'postalcode' => Yii::t('common', 'Postal code'),
            'province' => Yii::t('common', 'Province'),
            'city' => Yii::t('common', 'City'),
            'country' => Yii::t('common', 'Country')
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
	   		if(! empty($phoneNumber)){
			   $this->phoneextension = $phoneNumber->extension;
			   $this->phonenumber = $phoneNumber->number;
               $this->phonelabel = $phoneNumber->label_id;
	   	} 
			
		$address = Address::findByUserId($model->getId());
		if(! empty($address)){
		$this->address = $address->address;
		$this->addresslabel = $address->label;
		$this->city = $address->city_id;
		$this->country = $address->country_id;
		$this->province = $address->province_id;
		$this->postalcode = $address->postal_code;
		}	
		$this->qualifications = ArrayHelper::getColumn(
			Qualification::find()->where(['teacher_id'=>$model->getId()])->all(), 'program_id'
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
			$phonelabel = $this->phonelabel;
			$phoneextension = $this->phoneextension;
			$address = $this->address;
			$addresslabel = $this->addresslabel;
			$city = $this->city;
			$country = $this->country;
			$province = $this->province;
			$postalcode = $this->postalcode;
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

            $userLocationModel = UserLocation::findOne($model->getId());
			if(empty($userLocationModel)){
				$userLocationModel = new UserLocation();
			}
            $userLocationModel->user_id = $model->getId();
            $userLocationModel->location_id = Yii::$app->session->get('location_id');
            $userLocationModel->save();
			
            $userProfileModel = UserProfile::findOne($model->getId());
            $userProfileModel->lastname = $lastname;
            $userProfileModel->firstname = $firstname;
            $userProfileModel->save();
			
			$phoneNumberModel = PhoneNumber::findOne($model->getId());
			if(empty($phoneNumberModel)){
				$phoneNumberModel = new PhoneNumber();
				$phoneNumberModel->user_id = $model->getId();
			}
            $phoneNumberModel->extension = $phoneextension;
            $phoneNumberModel->number = $phonenumber;
            $phoneNumberModel->label_id = $phonelabel;
            $phoneNumberModel->save();

			$addressModel = Address::findByUserId($model->getId());
				if(empty($addressModel)){
					$addressModel = new Address();
			}
			$addressModel->city_id = $city;
			$addressModel->address = $address;
			$addressModel->label = $addresslabel;
			$addressModel->postal_code = $postalcode;
			$addressModel->country_id = $country;
			$addressModel->province_id = $province;
            $addressModel->save();
			$model->link('addresses', $addressModel);

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
            return !$model->hasErrors();
        }
        return null;
    }
}
