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
class LessonReview extends Model
{
    public $enrolmentType;
    public $rescheduleBeginDate;
    public $rescheduleEndDate;
    public $enrolmentIds;
    public $courseId;
    public $teacherId;
    public $changesFrom;
    public $isTeacherOnlyChanged;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enrolmentType', 'rescheduleBeginDate', 'rescheduleEndDate', 'enrolmentIds', 'courseId',
                'changesFrom', 'teacherId', 'isTeacherOnlyChanged'], 'safe']
        ];
    }

    public function getConflicts($lessons)
    {
        $conflicts = [];
        $conflictedLessonIds = [];
        $holidayConflictedLessonIds = [];
        foreach ($lessons as $draftLesson) {
            $draftLesson->setScenario(Lesson::SCENARIO_REVIEW);
            if (!$draftLesson->validate()) {
                $conflictedLessonIds[] = $draftLesson->id;
            }
            $conflicts[$draftLesson->id] = $draftLesson->getErrors('date');
            if ($draftLesson->isHolidayLesson()) {
                $holidayConflictedLessonIds[] = $draftLesson->id;
            }
        }

        $conflictedLessonIds = array_diff($conflictedLessonIds, $holidayConflictedLessonIds);
        return [
            'conflicts' => $conflicts,
            'lessonIds' => $conflictedLessonIds,
            'holidayConflictedLessonIds' => $holidayConflictedLessonIds
        ];
    }
}