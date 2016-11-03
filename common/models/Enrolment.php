<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "enrolment".
 *
 * @property string $id
 * @property string $courseId
 * @property string $studentId
 * @property int $isDeleted
 */
class Enrolment extends \yii\db\ActiveRecord
{
    public $studentIds;

    const PAYMENT_FREQUENCY_FULL = 1;
    const PAYMENT_FREQUENCY_MONTHLY = 2;
    const PAYMENT_FREQUENCY_QUARTERLY = 3;
    /**
     * {@inheritdoc}
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
                    'isDeleted' => true,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['courseId'], 'required'],
            [['courseId', 'studentId'], 'integer'],
            [['paymentFrequency', 'isDeleted', 'isConfirmed'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'studentId' => 'Student Name',
            'studentIds' => 'Enrolled Student Name',
            'isDeleted' => 'Is Deleted',
            'paymentFrequency' => 'Payment Frequency',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\EnrolmentQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\EnrolmentQuery(get_called_class());
    }

    public function notDeleted()
    {
        $this->where(['enrolment.isDeleted' => false]);

        return $this;
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId'])
            ->viaTable('course', ['id' => 'courseId']);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId']);
    }

    public static function paymentFrequencies()
    {
        return [
            self::PAYMENT_FREQUENCY_FULL => Yii::t('common', 'Full'),
            self::PAYMENT_FREQUENCY_MONTHLY => Yii::t('common', 'Monthly'),
            self::PAYMENT_FREQUENCY_QUARTERLY => Yii::t('common', 'Quarterly'),
        ];
    }
    public function afterSave($insert, $changedAttributes)
    {
        $isGroupProgram = (int) $this->course->program->type === (int) Program::TYPE_GROUP_PROGRAM;
        if ($isGroupProgram || (!empty($this->rescheduleBeginDate)) || (!$insert)) {
            return true;
        }
        $interval = new \DateInterval('P1D');
        $startDate = $this->course->startDate;
        $endDate = $this->course->endDate;
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $day) {
            if ((int) $day->format('N') === (int) $this->course->day) {
                $professionalDevelopmentDay = clone $day;
                $professionalDevelopmentDay->modify('last day of previous month');
                $professionalDevelopmentDay->modify('fifth '.$day->format('l'));
                if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
                    continue;
                }
                $lesson = new Lesson();
                $lesson->setAttributes([
                    'courseId' => $this->course->id,
                    'teacherId' => $this->course->teacherId,
                    'status' => Lesson::STATUS_DRAFTED,
                    'date' => $day->format('Y-m-d H:i:s'),
                    'duration' => $this->course->duration,
                    'isDeleted' => false,
                ]);
                $lesson->save();
            }
        }
    }
}
