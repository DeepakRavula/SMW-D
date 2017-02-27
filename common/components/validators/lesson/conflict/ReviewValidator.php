<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Holiday;
use common\models\Program;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;

class ReviewValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
       $intervals = $this->dateIntervals();
        $tree = new IntervalTree($intervals);
        $conflictedDates = [];
        $conflictedDatesResults = $tree->search(new \DateTime($model->date));
        foreach ($conflictedDatesResults as $conflictedDatesResult) {
            $startDate = $conflictedDatesResult->getStart();
            $conflictedDates[] = $startDate->format('Y-m-d');
        }
        $lessonIntervals = $this->lessonIntervals($model);
        $tree = new IntervalTree($lessonIntervals);
        $conflictedLessonIds = [];
        $conflictedLessonsResults = $tree->search(new \DateTime($model->date));
        foreach ($conflictedLessonsResults as $conflictedLessonsResult) {
            $conflictedLessonIds[] = $conflictedLessonsResult->id;
        }
        if ((!empty($conflictedDates)) || (!empty($conflictedLessonIds))) {
            $this->addError($model, $attribute, [
               'lessonIds' => $conflictedLessonIds,
               'dates' => $conflictedDates,
           ]);
        }
    }

	public function dateIntervals()
    {
        $holidays = Holiday::find()
            ->all();

        $intervals = [];
        foreach ($holidays as $holiday) {
            $intervals[] = new DateRangeInclusive(new \DateTime($holiday->date), new \DateTime($holiday->date));
        }
        
        return $intervals;
    }

    public function lessonIntervals($model)
    {
        $locationId = Yii::$app->session->get('location_id');
        $otherLessons = [];
        $intervals = [];

        if ((int) $model->course->program->type === (int) Program::TYPE_PRIVATE_PROGRAM) {
            $studentLessons = Lesson::find()
				->studentLessons($locationId, $model->course->enrolment->student->id)
				->all();
			
            foreach ($studentLessons as $studentLesson) {
				if(new \DateTime($studentLesson->date) == new \DateTime($model->date) && (int)$studentLesson->status === Lesson::STATUS_SCHEDULED){
					continue;
				}
                $otherLessons[] = [
                    'id' => $studentLesson->id,
                    'date' => $studentLesson->date,
                    'duration' => $studentLesson->course->duration,
                ];
            }
        }
        $teacherLessons = Lesson::find()
            ->teacherLessons($locationId, $model->teacherId)
            ->all();
        foreach ($teacherLessons as $teacherLesson) {
			$oldDate = $model->getOldAttribute('date');
			$oldTeacherId = $model->getOldAttribute('teacherId'); 
			if((int)$oldTeacherId == $model->teacherId && $oldDate == new \DateTime($model->date)) {
				if(new \DateTime($teacherLesson->date) == new \DateTime($model->date) && (int)$teacherLesson->status === Lesson::STATUS_SCHEDULED){
					continue;
				}
			}
            $otherLessons[] = [
                'id' => $teacherLesson->id,
                'date' => $teacherLesson->date,
                'duration' => $teacherLesson->course->duration,
            ];
        }
        $draftLessons = Lesson::find()
            ->where(['courseId' => $model->courseId, 'status' => Lesson::STATUS_DRAFTED])
            ->andWhere(['NOT', ['id' => $model->id]])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $otherLessons[] = [
                'id' => $draftLesson->id,
                'date' => $draftLesson->date,
                'duration' => $draftLesson->course->duration,
            ];
        }
        foreach ($otherLessons as $otherLesson) {
            $timebits = explode(':', $otherLesson['duration']);
            $intervals[] = new DateRangeInclusive(new \DateTime($otherLesson['date']), new \DateTime($otherLesson['date']), new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'), $otherLesson['id']);
        }

        return $intervals;
    }
}