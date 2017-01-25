<?php

namespace backend\models;

use common\models\User;
use common\models\UserProfile;
use common\models\UserAddress;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\Program;
use common\models\Qualification;
use common\models\UserLocation;
use common\models\TeacherAvailability;
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
    public $email;
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
    public $locations;
    private $model;
    public $phoneNumbers;
    public $addresses;
    public $section;

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
            ['lastname', 'string', 'min' => 2, 'max' => 255], ['email', 'filter', 'filter' => 'trim'],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],

            [['status'], 'integer'],
            [['qualifications'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Program::find()->active()->all(),
                    'id'
                )],
            ],
            ['roles', 'required'],
            [['locations', 'phonelabel', 'phoneextension', 'phonenumber', 'address', 'section'], 'safe'],
            [['addresslabel', 'postalcode', 'province', 'city', 'country'], 'safe'],
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
            'phonelabel' => Yii::t('common', 'Phone Label'),
            'phonenumber' => Yii::t('common', 'Phone Number'),
            'phoneextension' => Yii::t('common', 'Phone Extension'),
            'address' => Yii::t('common', 'Address'),
            'addresslabel' => Yii::t('common', 'Address Label'),
            'postalcode' => Yii::t('common', 'Postal code'),
            'province' => Yii::t('common', 'Province'),
            'city' => Yii::t('common', 'City'),
            'country' => Yii::t('common', 'Country'),
            'teacherAvailabilityDay' => Yii::t('common', 'Day'),
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
        $this->email = $model->email;
        $this->status = $model->status;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );
        $this->roles = end($this->roles);

        if (count($model->phoneNumbers) > 0) {
            $this->phoneNumbers = $model->phoneNumbers;
        } else {
            $this->phoneNumbers = [new PhoneNumber()];
        }

        if (count($model->addresses) > 0) {
            $this->addresses = $model->addresses;
        } else {
            $this->addresses = [new Address()];
        }

        $userFirstName = UserProfile::findOne(['user_id' => $model->getId()]);
        if (!empty($userFirstName)) {
            $this->firstname = $userFirstName->firstname;
            $this->lastname = $userFirstName->lastname;
        }

        $phoneNumber = PhoneNumber::findOne(['user_id' => $model->getId()]);
        if (!empty($phoneNumber)) {
            $this->phoneextension = $phoneNumber->extension;
            $this->phonenumber = $phoneNumber->number;
            $this->phonelabel = $phoneNumber->label_id;
        }

        $address = Address::findByUserId($model->getId());

        if (!empty($address)) {
            $this->address = $address->address;
            $this->addresslabel = $address->label;
            $this->city = $address->city_id;
            $this->country = $address->country_id;
            $this->province = $address->province_id;
            $this->postalcode = $address->postal_code;
        }

        $this->qualifications = ArrayHelper::getColumn(
            Qualification::find()->where(['teacher_id' => $model->getId()])->all(), 'program_id'
        );
        $this->qualifications = array_map('strval', $this->qualifications);

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
            $model->email = $this->email;
            if ($isNewRecord) {
                $model->status = User::STATUS_ACTIVE;
            } else {
                $model->status = $this->status;
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

            $userLocationModel = UserLocation::findOne(['user_id' => $model->getId(), 'location_id' => Yii::$app->session->get('location_id')]);
            if (empty($userLocationModel) && $this->roles !== User::ROLE_ADMINISTRATOR) {
                $userLocationModel = new UserLocation();
                $userLocationModel->user_id = $model->getId();
                $userLocationModel->location_id = Yii::$app->session->get('location_id');
                $userLocationModel->save();
            }

            $userProfileModel = UserProfile::findOne(['user_id' => $model->getId()]);
            if (empty($userProfileModel)) {
                $userProfileModel = new UserProfile();
            }
            $userProfileModel->lastname = $lastname;
            $userProfileModel->firstname = $firstname;
            $userProfileModel->save();

            $phoneNumberModel = PhoneNumber::findOne(['user_id' => $model->getId()]);
            if (empty($phoneNumberModel) || ($phoneNumberModel->label_id != $phonelabel)) {
                $phoneNumberModel = new PhoneNumber();
                $phoneNumberModel->user_id = $model->getId();
            }
            $phoneNumberModel->extension = $phoneextension;
            $phoneNumberModel->number = $phonenumber;
            $phoneNumberModel->label_id = $phonelabel;
            $phoneNumberModel->save();

            $addressModel = Address::findByUserId($model->getId());
            if (empty($addressModel) || ($addressModel->label != $addresslabel)) {
                $addressModel = new Address();
            }
            $addressModel->city_id = $city;
            $addressModel->address = $address;
            $addressModel->label = $addresslabel;
            $addressModel->postal_code = $postalcode;
            $addressModel->country_id = $country;
            $addressModel->province_id = $province;
            $addressModel->save();

            $userAddressModel = UserAddress::findOne(['user_id' => $model->getId()]);
            if (empty($userAddressModel)) {
                $userAddressModel = new UserAddress();
            }
            $userAddressModel->user_id = $model->id;
            $userAddressModel->address_id = $addressModel->id;
            $userAddressModel->save();

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
