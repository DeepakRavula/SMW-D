<?php

namespace common\models;

use yii\behaviors\SluggableBehavior;
use common\models\LocationDebt;
use common\models\LocationPaymentPreference;
use Carbon\Carbon;
use Yii;
use common\models\User;
use yii\db\Query;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use backend\models\UserForm;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property int $city_id
 * @property int $province_id
 * @property string $postal_code
 * @property int $country_id
 * @property string $from_time
 * @property string $to_time
 */
class Location extends \yii\db\ActiveRecord
{
    const DEFAULT_LOCATION = 1;
    public $royaltyValue;
    public $advertisementValue;

    
    /**
      * {@inheritdoc}
      */
    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                //'slugAttribute' => slug,
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }

    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'phone_number', 'city_id', 'province_id', 'postal_code', 'royaltyValue', 'advertisementValue', 'email'], 'required'],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['slug', 'conversionDate', 'email', 'isDeleted', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn', 'isEnabledCron'], 'safe'],
            [['royaltyValue', 'advertisementValue'], 'number'],
            [['city_id', 'province_id', 'country_id'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 64],
            [['postal_code'], 'string', 'max' => 16],
            [['name', 'address', 'postal_code', 'slug', 'email'], 'trim']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'phone_number' => 'Phone Number',
            'city_id' => 'City',
            'province_id' => 'Province',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country',
            'slug' => 'Slug',
            'royaltyValue' => 'Royalty (%)',
            'advertisementValue' => 'Advertisement (%)',
            'isEnabledCron' => 'Enable Cron',
        ];
    }
    
    public static function find()
    {
        return new \common\models\query\LocationQuery(get_called_class());
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }

    public function getRoyalty()
    {
        return $this->hasOne(LocationDebt::className(), ['locationId' => 'id'])
            ->onCondition(['location_debt.type' => LocationDebt::TYPE_ROYALTY]);
    }

    public function getAdvertisement()
    {
        return $this->hasOne(LocationDebt::className(), ['locationId' => 'id'])
            ->onCondition(['location_debt.type' => LocationDebt::TYPE_ADVERTISEMENT]);
    }

    public function getWalkinCustomer()
    {
        return $this->hasOne(LocationWalkinCustomer::className(), ['locationId' => 'id']);
    }

    public function getLocationAvailabilities()
    {
        return $this->hasMany(LocationAvailability::className(), ['locationId' => 'id']);
    }

    public function getUserLocations()
    {
        return $this->hasMany(UserLocation::className(), ['location_id' => 'id']);
    }

    public function getCronStatus()
    {
        return $this->isEnabledCron ? 'Yes' : 'No';
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!empty($this->conversionDate)) {
            $this->conversionDate = Carbon::parse($this->conversionDate)->format('Y-m-d H:i:s');
        }
        if ($insert) {
            $this->country_id = 1;
            $this->isDeleted = false;
        }
       
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $model = new LocationAvailability();
            $model->locationId = $this->id;
            $model->fromTime   = LocationAvailability::DEFAULT_FROM_TIME;
            $model->toTime     = LocationAvailability::DEFAULT_TO_TIME;
            for ($day = 1; $day < 8; $day ++) {
                $model->id          = null;
                $model->isNewRecord = true;
                $model->day         = $day;
                $model->type        = LocationAvailability::TYPE_OPERATION_TIME;
                $model->save();
                $model->id          = null;
                $model->isNewRecord = true;
                $model->type        = LocationAvailability::TYPE_SCHEDULE_TIME;
                $model->save();
            }
            $locationDebt = new LocationDebt();
            $locationDebt->locationId = $this->id;
            $locationDebt->type = LocationDebt::TYPE_ROYALTY;
            $locationDebt->value = $this->royaltyValue;
            $locationDebt->save();
            
            $locationDebt->id = null;
            $locationDebt->isNewRecord = true;
            $locationDebt->type = LocationDebt::TYPE_ADVERTISEMENT;
            $locationDebt->value = $this->advertisementValue;
            $locationDebt->save();

            $locationPaymentPreference = new LocationPaymentPreference();
            $locationPaymentPreference->locationId = $this->id;
            $locationPaymentPreference->isPreferredPaymentEnabled = false;
            $locationPaymentPreference->save();

            $this->addPermission();
            $this->addWalkinCustomer();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function getActiveStudentsCount($fromDate, $toDate)
    {
        $activeStudentsCount = Student::find()
            ->notDeleted()
            ->location($this->id)
            ->statusActive()
            ->distinct(['enrolment.studentId'])
            ->count();
        return $activeStudentsCount;
    }

    public function getActiveEnrolmentsCount($fromDate, $toDate)
    {

        $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
        if (!$fromDate && !$toDate) {
            $fromDate = $currentDate;
            $toDate = $currentDate;
        }
        $activeEnrolmentsCount =  Enrolment::find()
            ->joinWith(['course' => function ($query) use ($fromDate, $toDate) {
            $query->joinWith(['lessons' => function ($query) {
                $query->andWhere(['NOT', ['lesson.id' => null]]);
            }])
                ->location($this->id)
                ->overlap(Carbon::parse($fromDate)->format('Y-m-d'), Carbon::parse($toDate)->format('Y-m-d'))
                ->regular()
                ->confirmed()
                ->notDeleted();
        }])
        ->groupBy('course.id')
        ->count();

        return $activeEnrolmentsCount;
    }
    
    public function getRevenue($fromDate, $toDate)
    {
        $invoiceTaxTotal = Invoice::find()
            ->andWhere(['location_id' => $this->id, 'type' => Invoice::TYPE_INVOICE])
            ->andWhere(['between', 'date', (new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d')])
            ->notDeleted()
            ->sum('tax');

        $payments = Payment::find()
            ->exceptAutoPayments()
            ->exceptGiftCard()
            ->location($this->id)
            ->notDeleted()
            ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d')])
            ->sum('payment.amount');

        $royaltyPayment = InvoiceLineItem::find()
                ->notDeleted()
            ->joinWith(['invoice i' => function ($query) {
                $query->andWhere(['i.location_id' => $this->id, 'type' => Invoice::TYPE_INVOICE]);
            }])
            ->andWhere(['between', 'i.date', (new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d')])
            ->royaltyFree()
            ->sum('invoice_line_item.amount');

        $total = $payments - $invoiceTaxTotal - $royaltyPayment;

        return $total;
    }
    
    public function getLocationDebt($locationDebt, $fromDate, $toDate)
    {
        $locationDebtValue = 0;
        $revenue = $this->getRevenue($fromDate, $toDate);
        if (!empty($revenue) && $revenue>0) {
            if ((int)$locationDebt === (int)LocationDebt::TYPE_ROYALTY) {
                $royaltyValue = $this->royalty->value;
                $locationDebtValue = $revenue * (($royaltyValue) / 100);
            } elseif ((int)$locationDebt === (int)LocationDebt::TYPE_ADVERTISEMENT) {
                $advertisementValue = $this->advertisement->value;
                $revenue = $this->getRevenue($fromDate, $toDate);
                $locationDebtValue = $revenue * (($advertisementValue) / 100);
            }
        }
        return $locationDebtValue;
    }
    
    public function subTotal($fromDate, $toDate)
    {
        $royaltyValue = $this->getLocationDebt(LocationDebt::TYPE_ROYALTY, $fromDate, $toDate);
        $advertisementValue = $this->getLocationDebt(LocationDebt::TYPE_ADVERTISEMENT, $fromDate, $toDate);
        $subTotal = $royaltyValue + $advertisementValue;
        return $subTotal;
    }
    
    public function getTaxAmount($fromDate, $toDate)
    {
        $taxCode = TaxCode::find()
        ->andWhere(['province_id' => $this->province_id,
            'tax_type_id' => TaxType::HST
        ])
        ->orderBy(['id' => SORT_DESC])
        ->one();
        $taxPercentage = $taxCode->rate;
        $subTotal=$this->subTotal($fromDate, $toDate);
        $taxAmount=$subTotal * ($taxPercentage / 100);
        return $taxAmount;
    }
    
    public function addPermission() 
    {
    	$auth = Yii::$app->authManager;
        $ownerRole = User::ROLE_OWNER;
        $staffRole = User::ROLE_STAFFMEMBER;
        $staffPermissions = $this->staffPermissions();
        $ownerPermissions = $this->ownerPermissions();
        $permissions = $auth->getPermissions();
        $command = Yii::$app->db->createCommand();
        foreach ($permissions as $permission) {
            if (in_array($permission->name, $ownerPermissions)) {
                $command->insert('rbac_auth_item_child', array(
                    'parent' => $ownerRole,
                    'child' => $permission->name,
                    'location_id' => $this->id
                ))->execute();
            } else if(in_array($permission->name, $staffPermissions)) {
                $command->insert('rbac_auth_item_child', array(
                    'parent' => $ownerRole,
                    'child' => $permission->name,
                    'location_id' => $this->id
                ))->execute();
                $command->insert('rbac_auth_item_child', array(
                    'parent' => $staffRole,
                    'child' => $permission->name,
                    'location_id' => $this->id
                ))->execute();
            }
            $command->insert('rbac_auth_item_child', array(
                'parent' => User::ROLE_ADMINISTRATOR,
                'child' => $permission->name,
                'location_id' => $this->id
            ))->execute();
        }
    }
    
    public function adminPermissions() 
    {
        return [
            'manageAdminArea',
            'manageAdmin',
            'managePrograms',
            'manageCities',
            'manageProvinces',
            'manageCountries',
            'manageTaxes',
            'manageReminderNotes',
            'manageColorCode',
            'manageItemCategory',
            'manageBlogs',
            'manageHolidays',
            'manageEmailTemplate',
            'manageOwners',
            'manageAccessControl',
            'manageAllLocations',
            'manageReleaseNotes',
        ];
    }
    
    public function ownerPermissions() 
    {
        return [
            'teacherQualificationRate',
            'manageBirthdays',
            'managePrivileges',
            'manageRoyalty',
            'manageReports',
            'manageTaxCollected',
            'manageRoyaltyFreeItems',
            'manageItemsByCustomer',
            'manageItemReport',
            'manageItemCategoryReport',
            'manageDiscountReport',
            'manageLocations',
            'manageStaff',
            'manageImport',
            'manageClassrooms',
            'manageSetupArea',
            'manageMonthlyRevenue',
            'manageEnrolmentGains',
            'manageEnrolmentLosses',
            'manageInstructionHours',
            'manageAccountReceivableReport',
            'managePaymentsReport',
            'manageSalesAndPayment',
            'manageRecurringPayment',
        ];
    }
    
    public function staffPermissions() 
    {
        return [
            'loginToBackend',
            'manageDashboard',
            'manageCustomers',
            'manageEnrolments',
            'manageGroupLessons',
            'manageInvoices',
            'manageItems',
            'managePayments',
            'managePfi',
            'managePrivateLessons',
            'manageSchedule',
            'manageStudents',
            'manageTeachers',
            'viewBlogList',
        ];
    }

    public function addWalkinCustomer()
    {
        $customerForm = new UserForm();
        $customerForm->roles = User::ROLE_CUSTOMER;
        $customerForm->canLogin = true;
        $customerForm->status = User::STATUS_ACTIVE;
        $customerForm->canMerge = false;
        $customerForm->firstname = 'Walkin';
        $customerForm->lastname = 'Customer';
        $customerForm->locationId = $this->id;
        $customerId = $customerForm->save();
        $walkin = new LocationWalkinCustomer();
        $walkin->locationId = $this->id;
        $walkin->customerId = $customerId;
        $walkin->save();
        return true;
    }

    public function getLocationPaymentPreference()
    {
        return $this->hasOne(LocationPaymentPreference::className(), ['locationId' => 'id']);
    }
}
