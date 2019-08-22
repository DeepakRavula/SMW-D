<?php

namespace backend\models;

use common\models\User;
use common\models\UserProfile;
use common\models\log\UserLog;
use common\models\Location;
use common\models\UserLocation;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form.
 */
class UserForm extends Model
{
    const SCENARIO_CREATE = 'create';

    public $canLogin;
    public $canMerge;
    public $pin;
    public $locationId;
    public $username;
    public $status;
    public $roles;
    public $lastname;
    public $firstname;
    public $locations;
    private $model;
    private $password;
    private $confirmPassword;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['firstname', 'filter', 'filter' => 'trim'],
            ['firstname', 'required',  'on' => User::SCENARIO_CREATE],

            ['lastname', 'filter', 'filter' => 'trim'],
            ['lastname', 'required',  'on' => User::SCENARIO_CREATE ],
            ['pin', 'integer', 'min' => 1111, 'max' => 9999],
            ['pin', 'validatePin'],
            [['status'], 'integer'],
            ['roles', 'required'],
            [['locations', 'pin', 'canLogin', 'canMerge', 'locationId'], 'safe'],
            [['password', 'confirmPassword'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => "Confirm Password doesn't match with the password"],
        ];
    }

    /**
     * {@inheritdoc}
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
            'password' => Yii::t('common', 'Password'),
            'confirmPassword' => Yii::t('common', 'Confirm Password'),
        ];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = trim($value);
    }

    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword($value)
    {
        $this->confirmPassword = trim($value);
    }
    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->canLogin = $model->canLogin;
        $this->canMerge = $model->canMerge;
        $this->status = $model->status;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );
        $this->roles = end($this->roles);
      
        $userFirstName = UserProfile::findOne(['user_id' => $model->getId()]);
        if (!empty($userFirstName)) {
            $this->firstname = $userFirstName->firstname;
            $this->lastname = $userFirstName->lastname;
        }
        
        return $this->model;
    }
    
    public function validatePin()
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $pin = md5($this->pin);
        $user = User::find()
                    ->location($locationId)
                    ->andWhere(['pin_hash' => $pin])
                    ->notDeleted()
                    ->one();
        if ($user) {
            $this->addError('pin', Yii::t('backend', 'Try different pin.'));
        }
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
     *
     * @return User|null the saved model or null if saving fails
     *
     * @throws Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            $model->username = $this->username;
            if ($isNewRecord) {
                if ($this->roles == User::ROLE_CUSTOMER) {
                    $status = User::STATUS_NOT_ACTIVE;
                } else {
                    $status = User::STATUS_ACTIVE;
                }
                $model->status = $status;
            } else {
                $model->status = $this->status;
            }

            $lastname = $this->lastname;
            $firstname = $this->firstname;
            $model->canLogin = $this->canLogin;
            $model->canMerge = $this->canMerge;
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }
            if ($isNewRecord) {
                $model->roles = $this->roles;
                $model->afterSignup();
            }

            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if ($this->roles != null) {
                $auth->assign($auth->getRole($this->roles), $model->getId());
            }

            $userLocationModel = UserLocation::findOne(['user_id' => $model->getId(), 'location_id' => $this->locationId]);
            if (empty($userLocationModel) && $this->roles !== User::ROLE_ADMINISTRATOR) {
                $userLocationModel = new UserLocation();
                $userLocationModel->user_id = $model->getId();
                $userLocationModel->location_id = $this->locationId;
                $userLocationModel->save();
            }

            $userProfileModel = UserProfile::findOne(['user_id' => $model->getId()]);
            if (empty($userProfileModel)) {
                $userProfileModel = new UserProfile();
            }
            $userProfileModel->lastname = $lastname;
            $userProfileModel->firstname = $firstname;
            $userProfileModel->save();
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
            $roles = Yii::$app->authManager->getRolesByUser($userProfileModel->user_id);
            $role = end($roles);
            return $model->id;
        }

        return null;
    }

    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model = new $modelClass();
        $formName = $model->formName();
        $post = Yii::$app->request->post($formName);
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass();
                }
            }
        }
        unset($model, $formName, $post);

        return $models;
    }
}
