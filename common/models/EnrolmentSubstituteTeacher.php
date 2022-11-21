<?php

namespace common\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
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
            //['enrolmentIds', 'validateSameProgram'],
            ['changesFrom', function ($attribute, $params) {
                $currentDate = (new \DateTime())->format('Y-m-d');
                $changesFrom = (new \DateTime($this->changesFrom))->format('Y-m-d');
                if ($currentDate > $changesFrom) {
                    return $this->addError($attribute, 'Effect from cannot be in past date');
                }
            }, 'on' => self::SCENARIO_CHANGE],
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

    public function substitute()
    {
        $unConfirmedLessons = Lesson::find()
            ->notConfirmed()
            ->enrolment($this->enrolmentIds)
            ->all();
        
        $courseIds = ArrayHelper::getColumn($unConfirmedLessons, function ($element) {
            return $element->courseId;
        });
        $courseIds = array_unique($courseIds);
        Lesson::deleteAll([
            'courseId' => $courseIds,
            'isConfirmed' => false,
        ]);
        $changesFrom = (new \DateTime($this->changesFrom))->format('Y-m-d');
        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->andWhere(['>=', 'DATE(lesson.date)', $changesFrom])
            ->enrolment($this->enrolmentIds)
            ->notCanceled()
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        foreach ($lessons as $lesson) {
            $newLesson = new Lesson();
            $newLesson = clone $lesson;
            $newLesson->isNewRecord = true;
            $newLesson->id = null;
            $newLesson->teacherId = $this->teacherId;
            $newLesson->isConfirmed = false;
            $newLesson->save();
        }
        return true;
    }
}
