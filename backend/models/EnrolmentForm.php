<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $startDate
 * @property string $endDate
 */
class EnrolmentForm extends Model
{
    const SCENARIO_BASIC = 'enrollment-basic';
    const SCENARIO_DETAILED = 'enrolment-detail';
    const SCENARIO_CUSTOMER = 'enrollment-customer';
    const SCENARIO_STUDENT = 'enrolment-student';
    const SCENARIO_DATE_DETAILED = 'enrolment-start-date';

    public $programId;
    public $teacherId;
    public $day;
    public $fromTime;
    public $startDate;
    public $programRate;
    public $paymentFrequency;
    public $pfDiscount;
    public $enrolmentDiscount;
    public $duration;
    public $lessonsCount;
    public $autoRenew;

    public $firstname;
    public $isReverse;
    public $lastname;
    public $email;
    public $labelId;
    public $number;
    public $extension;
    public $phoneLabelId;
    public $addressLabelId;
    public $address;
    public $cityId;
    public $provinceId;
    public $countryId;
    public $postalCode;

    public $first_name;
    public $last_name;
    public $birth_date;
    public $gender;

    public $referralSourceId;
    public $description;

    public $paymentCycleStartDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programId', 'paymentFrequency', 'duration', 'startDate', 'programRate'],
                'safe'],
            [['fromTime', 'day', 'teacherId', 'isReverse'], 'safe'],
            [['programId', 'paymentFrequency', 'duration', 'programRate','lessonsCount'],
                'required', 'on' => self::SCENARIO_BASIC],
            [['enrolmentDiscount', 'pfDiscount'], 'safe'],
            [['startDate', 'paymentCycleStartDate'], 'required', 'on' => self::SCENARIO_DATE_DETAILED],
            [['paymentCycleStartDate'], 'safe', 'on' => self::SCENARIO_DEFAULT],
            [['fromTime', 'day', 'teacherId'], 'required', 'on' => self::SCENARIO_DETAILED],
            [['first_name', 'last_name'], 'required', 'on' => self::SCENARIO_STUDENT],
            [['firstname', 'lastname', 'email', 'labelId', 'number', 'phoneLabelId',
                'addressLabelId', 'address', 'cityId', 'countryId', 'provinceId'],
                'required', 'on' => self::SCENARIO_CUSTOMER],
            [['firstname', 'lastname', 'email', 'labelId', 'number', 'phoneLabelId',
                'addressLabelId', 'address', 'cityId', 'countryId', 'provinceId'],
                'safe'],
            [['postalCode', 'extension', 'first_name', 'last_name', 'gender', 'birth_date', 'lessonsCount', 'autoRenew', 'referralSourceId', 'description'], 'safe'],
        ];
    }
}
