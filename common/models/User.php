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
use common\models\Location;
use common\models\discount\CustomerDiscount;
use common\models\CustomerReferralSource;
use common\models\Label;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use common\models\log\LogHistory;
use common\models\log\LogObject;
use common\models\log\Log;
use common\models\Payment;

/**
 * User model.
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $logged_at
 * @property int $canLogin
 * @property string $password write-only password
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DRAFT = 3;

    const DEFAULT_ADMIN_EMAIL1 = 'tonia@arcadiamusicacademy.com';
    const DEFAULT_ADMIN_EMAIL2 = 'kristin@kristingreen.ca';
    const DEFAULT_ADMIN_EMAIL3 = 'senguttuvang@gmail.com';
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STAFFMEMBER = 'staffmember';
    const ROLE_OWNER = 'owner';
    const ROLE_GUEST = 'guest';
    const ROLE_BOT = 'bot';

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';
    const EVENT_AFTER_MERGE = 'afterMerge';

    const SCENARIO_MERGE = 'merge';
    const SCENARIO_DELETE = 'delete';

    const CONSOLE_USER_ID  = 727;

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
    public $locationId;
    public $roles;

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
                'replaceRegularDelete' => true
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
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
            ['email', 'validateOnDelete', 'on' => self::SCENARIO_DELETE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['customerId'], 'required', 'on' => self::SCENARIO_MERGE],
            ['customerId', 'validateCanMerge', 'on' => self::SCENARIO_MERGE],
            [['hasEditable', 'privateLessonHourlyRate', 'groupLessonHourlyRate', 'locationId',
                'customerId', 'isDeleted', 'pin_hash', 'canLogin', 'canMerge', 'roles'], 'safe']
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
            'showAllAdministrators' => Yii::t('common', 'Show All'),
            'showAllStaffMembers' => Yii::t('common', 'Show All'),
            'showAllOwners' => Yii::t('common', 'Show All'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    public function validateOnDelete($attribute)
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
        $role = end($roles);
        if ($this->isDefaultAdmin()) {
            $this->addError($attribute, 'Sorry! You can not delete super admins!.');
        } else if ($this->isCustomer()) {
            if (!empty($this->student)) {
                $this->addError($attribute, 'Unable to delete. There are student(s) associated with this ' . $role);
            } else if ($this->payments) {
                $this->addError($attribute, 'Unable to delete. There are payments associated with this ' . $role);
            }
        } else if ($this->isTeacher()) {
            if (!empty($this->qualifications) && !empty($this->courses)) {
                $this->addError($attribute, 'Unable to delete. There are qualification/course(s) associated with this ' . $role);
            }
        }
    }

    public function validateCanMerge($attribute)
    {
       
            $customer = self::findOne($this->customerId);
            if ($customer->hasInvoice() || $customer->hasPayments() || $customer->hasPaymentRequests()) {
                $this->addError($attribute, 'Sorry! You can not merge '
                    . $customer->publicIdentity . ' has payments/invoice/payment requests history.');
            }
    }

    public function isDefaultAdmin()
    {
        $userIds = [];
        $users = self::find()
            ->notDeleted()
            ->joinWith('primaryEmail')
            ->andWhere(['user_email.email' => [self::DEFAULT_ADMIN_EMAIL1,
                self::DEFAULT_ADMIN_EMAIL2, self::DEFAULT_ADMIN_EMAIL3]])
            ->all();
        foreach ($users as $user) {
            $userIds[] = $user->id;
        }
        if (in_array($this->id, $userIds)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserContacts()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id'])
            ->onCondition(['user_contact.isDeleted' => false]);
    }

    public function getUserToken()
    {
        return $this->hasOne(UserToken::className(), ['user_id' => 'id']);
    }

    public function getPrimaryContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id'])
            ->onCondition(['user_contact.isPrimary' => true, 'user_contact.isDeleted' => false]);
    }

    public function getBillingContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'id'])
            ->onCondition(['user_contact.labelId' => Label::LABEL_BILLING, 'user_contact.isDeleted' => false]);
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
        return $this->hasMany(Log::className(), ['id' => 'logId'], ['logObjectId' => LogObject::TYPE_USER ])
            ->via('logHistorys');
    }

    public function getLogHistorys()
    {
        return $this->hasMany(LogHistory::className(), ['instanceId' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'id'])
        ->onCondition(['payment.isDeleted' => false]);
    }

    public function getPaymentRequests()
    {
        return $this->hasMany(ProformaInvoice::className(), ['userId' => 'id'])
        ->onCondition(['proforma_invoice.isDeleted' => false]);
    }
    
    public function getTeacherLessons()
    {
        return $this->hasMany(Lesson::className(), ['teacherId' => 'id'])
            ->onCondition(['lesson.status' => [Lesson::STATUS_RESCHEDULED, Lesson::STATUS_SCHEDULED], 
                'lesson.isDeleted' => false, 'lesson.isConfirmed' => true]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['userContactId' => 'id'])
            ->onCondition(['user_address.isDeleted' => false])
            ->via('userContacts');
    }
    
    public function getPrimaryAddress()
    {
        return $this->hasOne(UserAddress::className(), ['userContactId' => 'id'])
            ->onCondition(['user_address.isDeleted' => false])
            ->via('primaryContact');
    }

    public function getPrimaryEmail()
    {
        return $this->hasOne(UserEmail::className(), ['userContactId' => 'id'])
            ->onCondition(['user_email.isDeleted' => false])
            ->via('primaryContact');
    }

    public function getBillingAddress()
    {
        return $this->hasOne(UserAddress::className(), ['userContactId' => 'id'])
            ->onCondition(['user_address.isDeleted' => false])
            ->via('billingContact');
    }

    public function getQualifications()
    {
        return $this->hasMany(Qualification::className(), ['teacher_id' => 'id'])
            ->onCondition(['qualification.isDeleted' => false]);
    }

    public function setActiveStatus()
    {
        $inactiveCount = 0;
        $studentCount = $this->students ? $this->studentsCount : 0;
        foreach ($this->students as $student) {
            if (!$student->isActive()) {
                $inactiveCount ++;
            }
        }
        if ($inactiveCount == $studentCount) {
            $status = self::STATUS_NOT_ACTIVE;
        } else {
            $status = self::STATUS_ACTIVE;
        }
        $this->updateAttributes(['status' => $status]);
        return true;
    }

    public function getQualifi($programId)
    {
        return Qualification::find()
            ->notDeleted()
            ->andWhere(['teacher_id' => $this->id, 'program_id' => $programId])
            ->one();
    }

    public function hasQualified($programIds)
    {
        foreach ($programIds as $programId) {
            if (!$this->getQualifi($programId)) {
                return false;
            }
        }
        return true;
    }

    public function getLocationWalkin()
    {
        return $this->hasMany(LocationWalkinCustomer::className(), ['customerId' => 'id']);
    }

    public function isLocationWalkin()
    {
        return !empty($this->locationWalkin);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->canLogin)) {
                $this->canLogin = false;
            }
            if (empty($this->canMerge)) {
                $this->canMerge = false;
            }
            if (empty($this->isDeleted)) {
                $this->isDeleted = false;
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) 
    {
        if ($this->locationId) {
            $userLocation = new UserLocation();
            $userLocation->user_id = $this->id;
            $userLocation->location_id = $this->locationId;
            $userLocation->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function beforeDelete() 
    {
        if ($this->students) {
            foreach ($this->students as $student) {
                $student->delete();
            }
        }
        return parent::beforeDelete();
    }

    public function getCustomerPaymentPreference()
    {
        return $this->hasOne(CustomerPaymentPreference::className(), ['userId' => 'id'])
                ->andWhere(['customer_payment_preference.isDeleted' => false]);
    } 

    public function getCustomerReferralSource()
    {
        return $this->hasOne(CustomerReferralSource::className(), ['userId' => 'id']);
    }
    
    public function getPhoneNumbers()
    {
        return $this->hasMany(UserPhone::className(), ['userContactId' => 'id'])
            ->onCondition(['user_phone.isDeleted' => false])
            ->via('userContacts');
    }

    public function getPhoneNumber()
    {
        return $this->hasOne(UserPhone::className(), ['userContactId' => 'id'])
            ->onCondition(['user_phone.isDeleted' => false])
            ->via('userContacts');
    }

    public function getPrimaryPhoneNumber()
    {
        return $this->hasOne(UserContact::className(), ['id' => 'userContactId'])
            ->via('phoneNumbers')
            ->onCondition(['user_contact.isPrimary' => true, 'user_contact.isDeleted' => CURLOPT_SSL_FALSESTART]);
    }

    public function getCustomerDiscount()
    {
        return $this->hasOne(CustomerDiscount::className(), ['customerId' => 'id']);
    }
    
    public function getEmails()
    {
        return $this->hasMany(UserEmail::className(), ['userContactId' => 'id'])
            ->onCondition(['user_email.isDeleted' => false])
            ->via('userContacts');
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

    public function hasLocation()
    {
        return !empty($this->location);
    }

    public function getAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::className(), ['teacher_location_id' => 'id'])
            ->onCondition(['teacher_availability_day.isDeleted' => false])
            ->viaTable('user_location', ['user_id' => 'id']);
    }

    public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['teacher_id' => 'id'])
            ->onCondition(['qualification.isDeleted' => false]);
    }

    public function getStudent()
    {
        return $this->hasMany(Student::className(), ['customer_id' => 'id'])
            ->onCondition(['student.isDeleted' => false]);
    }
    public function getStudentsList() 
    {
        $studentsList = [];
        $students = $this->student;
        foreach($students as $student) {
            $studentsList[] = $student->fullName;
        }
        return implode(", ", $studentsList);
    }
    public function getCourses()
    {
        return $this->hasMany(Course::className(), ['teacherId' => 'id'])
            ->onCondition(['course.isConfirmed' => true, 'course.isDeleted' => false]);
    }
    
    public function getEmail()
    {
        $email = UserEmail::find()
            ->notDeleted()
            ->joinWith(['userContact' => function ($query) {
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
            ->andWhere(['rbac_auth_assignment.item_name' => $role])
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
    
    public function setPin($pin)
    {
        $this->pin_hash = md5($pin);
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
        $model->roles = $this->roles;
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
                $query->andWhere(['user_profile.user_id' => $id]);
            }]);
        }])
		->notDeleted()
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
                $query->andWhere(['locationId' => Location::findOne(['slug' => \Yii::$app->location])->id])
                        ->confirmed()
                        ->notDeleted();
            }])
            ->andWhere(['lesson.teacherId' => $id])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED]]])
            ->isConfirmed()
            ->all();
        $events = [];
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
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
            ->andWhere(['raa.item_name' => 'customer'])
            ->andWhere(['ul.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id])
            ->notDeleted()
            ->joinWith(['student' => function ($query) {
                $query->active();
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
            ->andWhere(['raa.item_name' => 'teacher'])
            ->andWhere(['ul.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id])
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
            ->andWhere(['raa.item_name' => 'staffmember'])
            ->andWhere(['ul.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id])
            ->notDeleted()
            ->active()
            ->count();
    }
    public static function ownerCount()
    {
        return self::find()
            ->joinWith('userLocation ul')
            ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'owner'])
            ->andWhere(['ul.location_id' => Location::findOne(['slug' => \Yii::$app->location])->id])
            ->notDeleted()
            ->active()
            ->count();
    }
    public static function adminCount()
    {
        return self::find()
            ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
            ->andWhere(['raa.item_name' => 'administrator'])
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
            ->notDeleted();
    }

    public function getAllStudents()
    {
        return $this->hasMany(Student::className(), ['customer_id' => 'id']);
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

    public function hasPayments()
    {
        return !empty($this->payments);
    }

    public function hasPaymentRequests()
    {
        return !empty($this->paymentRequests);
    }
    
    public function isOwner()
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
        $role = end($roles);
        return $role === self::ROLE_OWNER;
    }

    public function isTeacher()
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
        $role = end($roles);
        return $role === self::ROLE_TEACHER;
    }
    
    public function isBackendUsers()
    {
        return $this->isAdmin() || $this->isOwner() || $this->isStaff();
    }

    public function isManagableByStaff()
    {
        return $this->isCustomer() || $this->isTeacher();
    }

    public function isManagableByOwner()
    {
        return $this->isManagableByStaff() || $this->isStaff();
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
    
    public function hasPin()
    {
        return $this->isStaff() || $this->isOwner();
    }
    
    public function canManagePin()
    {
        return $this->isAdmin() || $this->isOwner();
    }
    
    public function hasPrimaryEmail()
    {
        return !empty($this->primaryEmail);
    }

    public function getStatus() 
    {
        $status = null;
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                $status = 'Active';
            break;
            case self::STATUS_NOT_ACTIVE:
                $status = 'Inactive';
            break;
        }
        return $status;
    }

    public function getLessonsDue($id)
    {
        $invoicedLessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->customer($id)
            ->invoiced();
        $lessonsOwingAmount = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->dueLessons()
            ->privateLessons()
            ->joinWith(['privateLesson' => function ($query) use ($id) {
                $query->andWhere(['>', 'private_lesson.balance', 0.09]);
            }])
            ->customer($id)
            ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
            ->andWhere(['invoiced_lesson.id' => null])
            ->sum("private_lesson.balance");

        $invoicedLessonsQuery = GroupLesson::find()
            ->joinWith(['invoiceItemLessons' => function($query) {
                $query->joinWith(['invoiceLineItem ili' => function($query) {
                    $query->notDeleted()
                    ->joinWith(['invoice in' => function($query) {
                        $query->notDeleted();
                    }]);
                }]);
            }])
            ->joinWith(['invoiceItemsEnrolment' => function($query) {
                $query->joinWith(['lineItem' => function($query) {
                    $query->notDeleted()
                    ->joinWith(['invoice' => function($query) {
                        $query->notDeleted();
                    }]);
                }]);
            }]);
        $groupLessonsOwingAmount = GroupLesson::find()
            ->joinWith(['lesson' => function($query) {
                $query->notDeleted()
                    ->isConfirmed()
                    ->notCanceled();
            }])
            ->joinWith(['enrolment' => function($query) use ($id) {
                $query->notDeleted()
                    ->isConfirmed()
                    ->customer($id);
            }])
            ->leftJoin(['invoiced_lesson' => $invoicedLessonsQuery], 'group_lesson.id = invoiced_lesson.id')
            ->andWhere(['invoiced_lesson.id' => null])
            ->dueLessons()
            ->andWhere(['>', 'group_lesson.balance', 0.09])
            ->sum("group_lesson.balance");
        $lessonsDue = $lessonsOwingAmount + $groupLessonsOwingAmount;
        return $lessonsDue;
    }

    public function getInvoiceOwingAmountTotal($id)
    {
        $invoiceOwingAmount = Invoice::find()
                ->andWhere([
                    'invoice.user_id' => $id,
                    'invoice.type' => Invoice::TYPE_INVOICE,
                ])
                ->andWhere(['>', 'invoice.balance', 0.09])
                ->notDeleted()
                ->sum("invoice.balance");
                
        return $invoiceOwingAmount;
    }

    public function getTotalCredits($id) 
    {
        $invoiceCredits = Invoice::find()
            ->notDeleted()
            ->invoiceCredit($id)
            ->sum("invoice.balance"); 

        $paymentCredits = Payment::find()
            ->notDeleted()
            ->exceptAutoPayments()
            ->customer($id)
            ->credit()
            ->orderBy(['payment.id' => SORT_ASC])
            ->sum("payment.balance"); 

        $totalCredits = abs($invoiceCredits) + abs($paymentCredits);
        return $totalCredits;
    }
}
