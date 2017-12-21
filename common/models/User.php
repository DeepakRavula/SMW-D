<?php

namespace common\models;

use common\models\query\UserQuery;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use backend\models\UserForm;
use common\models\discount\CustomerDiscount;
use common\models\Label;

/**
 * User model.
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $logged_at
 * @property string $password write-only password
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DRAFT = 3;

    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STAFFMEMBER = 'staffmember';
    const ROLE_OWNER = 'owner';
    const ROLE_GUEST = 'guest';
    const ROLE_BOT = 'bot';

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    const SCENARIO_MERGE = 'merge';

    public $customerIds;
    public $customerId;
    public $fromDate;
	public $toDate;
	public $dateRange;	
	public $invoiceStatus;
	public $studentId;
	public $privateLessonHourlyRate;
	public $groupLessonHourlyRate;
	public $hasEditable;
	public $lessonId;	
	public static $roleNames = [
        self::ROLE_ADMINISTRATOR => 'Admin',
        self::ROLE_OWNER => 'Owner',
        self::ROLE_STAFFMEMBER => 'Staff Member',
        self::ROLE_TEACHER => 'Teacher',
    ];
    public static $roleBootstrapClasses = [
		self::ROLE_ADMINISTRATOR => 'danger',
        self::ROLE_OWNER => 'success',
        self::ROLE_STAFFMEMBER => 'info',
        self::ROLE_TEACHER => 'default',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key',
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString(),
            ],
            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token',
                ],
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                },
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status',
                ],
                ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'unique'],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['customerIds'], 'required', 'on' => self::SCENARIO_MERGE],
            ['customerIds', 'canMerge', 'on' => self::SCENARIO_MERGE],
            [['hasEditable', 'privateLessonHourlyRate', 'groupLessonHourlyRate',
                'customerId', 'isDeleted'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'E-mail'),
            'status' => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => Yii::t('common', 'Last login'),
            'customerId' => Yii::t('common', 'Customers'),
            'customerIds' => Yii::t('common', 'Selected Customers'),
            'showAllCustomers' => Yii::t('common', 'Show All'),
            'showAllTeachers' => Yii::t('common', 'Show All'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    public function canMerge($attribute)
    {
        foreach ($this->customerIds as $customerId) {
            $customer = self::findOne($customerId);
            if ($customer->hasInvoice()) {
                $this->addError($attribute, 'Sorry! You can not merge '
                    . $customer->publicIdentity . ' has payments/invoice history.');
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getUserContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id']);
    } 
	public function getPrimaryContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id'])
			->onCondition(['user_contact.isPrimary' => true]);
    } 
	public function getBillingContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id'])
			->onCondition(['user_contact.labelId' => Label::LABEL_BILLING]);	
    } 
    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'id']);
    }

    public function getNotes()
    {
        return $this->hasMany(Note::className(), ['instanceId' => 'id'])
            ->onCondition(['instanceType' => Note::INSTANCE_TYPE_USER]);
    }

    public function getLogs()
    {
        return $this->hasMany(timelineEvent\TimelineEventUser::className(), ['userId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['userContactId' => 'id'])
			->via('userContact');
    }
	
	public function getPrimaryAddress()
    {
		return $this->hasOne(UserAddress::className(), ['userContactId' => 'id'])
			->via('primaryContact');
    }
	public function getPrimaryEmail()
    {
		return $this->hasOne(UserEmail::className(), ['userContactId' => 'id'])
			->via('primaryContact');
    }
	public function getBillingAddress()
    {
		return $this->hasOne(UserAddress::className(), ['userContactId' => 'id'])
			->via('billingContact');
    }
	public function getQualifications()
	{
		return $this->hasMany(Qualification::className(), ['teacher_id' => 'id']);
	}

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
    
    public function getCustomerPaymentPreference()
    {
        return $this->hasOne(CustomerPaymentPreference::className(), ['userId' => 'id']);
    }
    
	public function getPhoneNumbers()
    {
        return $this->hasMany(UserPhone::className(), ['userContactId' => 'id'])
			->via('userContact');
    }
    public function getPhoneNumber()
    {
        return $this->hasOne(UserPhone::className(), ['userContactId' => 'id'])
			->via('userContact');
    }
	public function getPrimaryPhoneNumber()
    {
		return $this->hasOne(UserContact::className(), ['id' => 'userContactId'])
			->via('phoneNumbers')
			->onCondition(['user_contact.isPrimary' => true]);
	}	
	public function getCustomerDiscount()
    {
        return $this->hasOne(CustomerDiscount::className(), ['customerId' => 'id']);
    }
    
    public function getEmails()
    {
        return $this->hasMany(UserEmail::className(), ['userContactId' => 'id'])
			->via('userContact');
    }

	public function getPhone()
    {
        $phones = [];
        foreach ($this->phoneNumbers as $phoneNumber) {
            $phones[] = $phoneNumber->number;
        }
        return implode(", ", $phones);
    }
	public function getEmailNames()
    {
        $emails = [];
        foreach ($this->emails as $email) {
            $emails[] = $email->email;
        }
        return implode(", ", $emails);
    }
    
    public function getLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'id']);
    }

    public function getAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::className(), ['teacher_location_id' => 'id'])
          ->viaTable('user_location', ['user_id' => 'id']);
    }

    public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['teacher_id' => 'id']);
    }

    public function getStudent()
    {
        return $this->hasMany(Student::className(), ['customer_id' => 'id']);
    }
	 public function getCourses()
    {
        return $this->hasMany(Course::className(), ['teacherId' => 'id']);
    }
	
    public function getEmail()
    {
        $email = UserEmail::find()
			->joinWith(['userContact' => function($query) {
                $query->andWhere(['userId' => $this->id, 'isPrimary' => true]);
			}])
            ->one();
        return !empty($email) ? $email->email : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    public function hasDiscount()
    {
        return !empty($this->customerDiscount);
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['username' => $username, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Finds user by username or email.
     *
     * @param string $login
     *
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->active()
            ->andWhere([
                'and',
                ['or', ['username' => $login], ['email' => $login]],
                'status' => self::STATUS_ACTIVE,
            ])
            ->one();
    }

    public static function findByRole($role)
    {
        return static::find()
            ->join('LEFT JOIN', 'rbac_auth_assignment', 'rbac_auth_assignment.user_id = id')
            ->where(['rbac_auth_assignment.item_name' => $role])
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Returns user statuses list.
     *
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Inactive'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
			self::STATUS_DRAFT => Yii::t('common', 'Draft'),
        ];
    }

	public static function status()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Inactive'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
        ];
    }

    /**
     * Creates user profile and application event.
     *
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();
        
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);
        $this->trigger(self::EVENT_AFTER_SIGNUP);
        $model = new UserForm();
        $model->roles = Yii::$app->request->queryParams['role_name'];
        // Default role
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole($model->roles), $this->getId());
    }
    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }

        return $this->email;
    }

    public function getPublicIdentityWithEnrolment()
    {
        return !empty($this->enrolment) ? $this->publicIdentity . ' (' .
            $this->enroledStudents . ')' : null;
    }
    
    public function teacherAvailabilityWithLessons($id)
    {
        $teacherAvailabilities = TeacherAvailability::find()
        ->joinWith(['userLocation' => function ($query) use ($id) {
            $query->joinWith(['userProfile' => function ($query) use ($id) {
                $query->where(['user_profile.user_id' => $id]);
            }]);
        }])
        ->all();
        $availableHours = [];
        foreach ($teacherAvailabilities as $teacherAvailability) {
            $availableHours[] = [
                'start' => $teacherAvailability->from_time,
                'end' => $teacherAvailability->to_time,
                'dow' => [$teacherAvailability->day],
                'className' => 'teacher-available',
            ];
        }

        $lessons = [];
        $lessons = Lesson::find()
            ->joinWith(['course' => function ($query) {
                $query->andWhere(['locationId' => \Yii::$app->session->get('location_id')]);
            }])
            ->where(['lesson.teacherId' => $id])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED]]])
			->isConfirmed()
            ->all();
        $events = [];
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->fullDuration);
            $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
            } else {
                $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
            }
            $class = null;
            if (!$lesson->hasProFormaInvoice()) {
                if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
                    $class = 'proforma-paid';
                } else {
                    $class = 'proforma-unpaid';
                }
            }
            $events[] = [
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'className' => $class,
                'title' => $title,
            ];
        }
        unset($lesson);

        return [
            'availableHours' => $availableHours,
            'events' => $events,
        ];
    }

	public function isCustomer()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        $role  = end($roles);
        return $role->name === self::ROLE_CUSTOMER;
    }
	public function isWalkin()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        $role  = end($roles);
        return $role->name === self::ROLE_GUEST;
    }
	public function getRoleById($id)
    {
		$roles = Yii::$app->authManager->getRolesByUser($id);
		return end($roles)->name;
    }
    public function getRoleName()
    {
		$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
		$role = end($roles);
        return self::$roleNames[$role->name];
    }
    public function getRoleBootstrapClass()
    {
		$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
		$role = end($roles);
        return self::$roleBootstrapClasses[$role->name];
    }
	public static function customerCount()
    {
	 $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
                
            return self::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
			->where(['raa.item_name' => 'customer'])
			->andWhere(['ul.location_id' => \Yii::$app->session->get('location_id')])
                        ->notDeleted()
                        ->joinWith(['student' => function ($query) use ($currentDate) {
                            $query->enrolled($currentDate);
                            }])
			->active()
                        ->groupBy('user.id')
			->count();
    }
	
	public static function teacherCount()
    {
		return self::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
			->where(['raa.item_name' => 'teacher'])
			->andWhere(['ul.location_id' => \Yii::$app->session->get('location_id')])
                        ->joinWith(['userLocation' => function ($query) {
                            $query->joinWith('teacherAvailability');
                        }])
			->active()
                        ->notDeleted()
                        ->groupBy('user.id')        
			->count();
    }
	public static function staffCount()
    {
		return self::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
			->where(['raa.item_name' => 'staffmember'])
			->andWhere(['ul.location_id' => \Yii::$app->session->get('location_id')])
                        ->notDeleted()
			->active()
			->count();
    }
	public static function ownerCount()
    {
		return self::find()
			->joinWith('userLocation ul')
			->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
			->where(['raa.item_name' => 'owner'])
			->andWhere(['ul.location_id' => \Yii::$app->session->get('location_id')])
                        ->notDeleted()
			->active()
			->count();
    }
	public static function adminCount()
    {
		return self::find()
			->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
			->where(['raa.item_name' => 'administrator'])
                        ->notDeleted()
			->active()
			->count();
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['user_id' => 'id'])
            ->notDeleted();
    }

    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['customer_id' => 'id'])
            ->active()->notDeleted();
    }

    public function getEnroledStudents()
    {
        return $this->studentsCount == 1 ? 'has ' . $this->studentsCount . ' student enrolled' :
            'has ' . $this->studentsCount . ' students enrolled';
    }

    public function getStudentsCount()
    {
        return count($this->students);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['studentId' => 'id'])
            ->via('students');
    }

    public function hasInvoice()
    {
        return !empty($this->invoice);
    }
    
    public function isAdmin()
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
        $role = end($roles);
        return $role === self::ROLE_ADMINISTRATOR;
    }
     public function isStaff()
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
        $role = end($roles);
        return $role === self::ROLE_STAFFMEMBER;
    }
}
