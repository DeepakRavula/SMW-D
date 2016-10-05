<?php

namespace common\models;

use Yii;
use common\models\Holiday;
use common\models\ProfessionalDevelopmentDay;
use common\models\Lesson;
use \yii2tech\ar\softdelete\SoftDeleteBehavior;
/**
 * This is the model class for table "enrolment".
 *
 * @property string $id
 * @property string $courseId
 * @property string $studentId
 * @property integer $isDeleted
 */
class Enrolment extends \yii\db\ActiveRecord
{
	public $studentIds;
	public $fromTime;
	public $rescheduleBeginDate;
	
	const PAYMENT_FREQUENCY_FULL = 1;
	const PAYMENT_FREQUENCY_MONTHLY = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment';
    }

	public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true
                ],
            ],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'isDeleted'], 'required'],
            [['courseId', 'studentId', 'isDeleted',], 'integer'],
            [['paymentFrequency', 'fromTime', 'rescheduleBeginDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'studentId' => 'Student Name',
            'studentIds' => 'Enrolled Student Name',
            'isDeleted' => 'Is Deleted',
			'paymentFrequency' => 'Payment Frequency'
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\EnrolmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\EnrolmentQuery(get_called_class());
    }

	public function notDeleted() {
		$this->where(['enrolment.isDeleted' => false]);
		
		return $this;
	}
    
	public function getCourse() {
		return $this->hasOne(Course::className(), ['id' => 'courseId']);
	}

	public function getStudent() {
		return $this->hasOne(Student::className(), ['id' => 'studentId']);
	}

	public function getProgram() {
		return $this->hasOne(Program::className(), ['id' => 'programId'])
			->viaTable('course',['id' => 'courseId']);
	}
	
	public function getLessons() {
		return $this->hasMany(Lesson::className(), ['courseId' => 'courseId']);
	}

	public static function paymentFrequencies(){
		return [
            self::PAYMENT_FREQUENCY_FULL => Yii::t('common', 'Full'),
			self::PAYMENT_FREQUENCY_MONTHLY => Yii::t('common', 'Monthly'),
		];
	}
	public function afterSave($insert, $changedAttributes)
    {
		$isGroupProgram = (int) $this->course->program->type === (int)Program::TYPE_GROUP_PROGRAM; 
        if($isGroupProgram || (! empty($this->rescheduleBeginDate)) || (! $insert)) {
            return true;
        }
        $interval = new \DateInterval('P1D');
        $startDate = $this->course->startDate;
        $endDate = $this->course->endDate;
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $period = new \DatePeriod($start, $interval, $end);
        $fromTime = new \DateTime($this->course->fromTime);
        $length = explode(':', $this->course->duration);
        $fromTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
        $toTime = $fromTime->format('H:i:s');

        $holidays = Holiday::find()->all();
        $pdDays = ProfessionalDevelopmentDay::find()->all();
        $holidayDays = [];
        $professionalDays = [];
        $leaveDays = [];
        if(! empty($holidays)){
            foreach($holidays as $holiday){
                $holiday = \DateTime::createFromFormat('Y-m-d H:i:s',$holiday->date);
                $holidayDays[] = $holiday->format('Y-m-d');
            }
        }

        if(! empty($pdDays)){
            foreach($pdDays as $pdDay){
                $pdDay = \DateTime::createFromFormat('Y-m-d H:i:s',$pdDay->date);
                $professionalDays[] = $pdDay->format('Y-m-d');
            }
        }

        $leaveDays = array_merge($holidayDays,$professionalDays);

        foreach($period as $day){
            foreach($leaveDays as $leaveDay){
                if($day->format('Y-m-d') === $leaveDay){
                    continue 2;
                }
            }

            if ((int) $day->format('N') === (int) $this->course->day) {
                $lesson = new Lesson();
                $lesson->setAttributes([
                    'courseId'	 => $this->course->id,
                    'teacherId' => $this->course->teacherId,
                    'status' => Lesson::STATUS_DRAFTED,
                    'date' => $day->format('Y-m-d H:i:s'),
                    'toTime' => $toTime, 
                    'isDeleted' => 0,
                ]);
                $lesson->save();
            }
        }
	}
}
