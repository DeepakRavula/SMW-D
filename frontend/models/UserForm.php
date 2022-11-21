<?php

namespace frontend\models;

use common\models\User;
use common\models\UserProfile;
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
    public $username;
    public $status;
    public $roles;
    public $lastname;
    public $firstname;
    public $locations;
    private $model;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['firstname', 'filter', 'filter' => 'trim'],
            ['firstname', 'required', 'on' => 'create'],
            ['firstname', 'string', 'min' => 2, 'max' => 255],

            ['lastname', 'filter', 'filter' => 'trim'],
            ['lastname', 'required', 'on' => 'create'],
            ['lastname', 'string', 'min' => 2, 'max' => 255],
            [['status'], 'integer'],
            ['roles', 'required'],
            [['locations'], 'safe'],
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
        ];
    }

    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->username = $model->username;
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
                $model->status = User::STATUS_ACTIVE;
            } else {
                $model->status = $this->status;
            }

            $lastname = $this->lastname;
            $firstname = $this->firstname;
         
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }
            if ($isNewRecord) {
                $model->afterSignup();
            }

            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if ($this->roles != null) {
                $auth->assign($auth->getRole($this->roles), $model->getId());
            }

            $userLocationModel = UserLocation::findOne(['user_id' => $model->getId(), 'location_id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]);
            if (empty($userLocationModel) && $this->roles !== User::ROLE_ADMINISTRATOR) {
                $userLocationModel = new UserLocation();
                $userLocationModel->user_id = $model->getId();
                $userLocationModel->location_id = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                $userLocationModel->save();
            }

            $userProfileModel = UserProfile::findOne(['user_id' => $model->getId()]);
            if (empty($userProfileModel)) {
                $userProfileModel = new UserProfile();
            }
            $userProfileModel->lastname = $lastname;
            $userProfileModel->firstname = $firstname;
            $userProfileModel->save();
           
            return !$model->hasErrors();
        }

        return null;
    }
}
