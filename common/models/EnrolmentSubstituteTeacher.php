<?php

namespace common\models;

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
class EnrolmentSubstituteTeacher extends Model
{
    public $enrolmentIds;
    public $teacherId;
    public $changesFrom;

    const SCENARIO_CHANGE = 'teacher-change';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacherId', 'changesFrom'], 'required', 'on' => self::SCENARIO_CHANGE],
            ['enrolmentIds', 'validateSameTeacher'],
            ['enrolmentIds', 'validateSameProgram']
        ];
    }

    public function attributeLabels()
    {
        return [
            'changesFrom' => 'Effect From',
            'teacherId' => 'Teacher'
        ];
    }

    public function validateSameTeacher($attributes)
    {
        $enrolments = Enrolment::find()
            ->andWhere(['id' => $this->enrolmentIds])
            ->all();
        $teacherId = end($enrolments)->course->teacherId;
        foreach ($enrolments as $enrolment) {
            if ($enrolment->course->teacherId != $teacherId) {
                $this->addError($attributes, "Choose enrolments with same teacher!");
                break;
            }
        }
    }

    public function validateSameProgram($attributes)
    {
        $enrolments = Enrolment::find()
            ->andWhere(['id' => $this->enrolmentIds])
            ->all();
            $programId = end($enrolments)->course->programId;
            foreach ($enrolments as $enrolment) {
                if ($enrolment->course->programId != $programId) {
                    $this->addError($attributes, "Choose enrolments with same program!");
                    break;
                }
            }
    }
}
