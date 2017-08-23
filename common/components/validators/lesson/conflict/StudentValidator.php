<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;
use common\models\Vacation;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use League\Period\Period;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->isExtra()) {
            $studentId = $model->studentId; 
        } else if($model->course->program->isPrivate()) {
            $studentId = $model->course->enrolment->student->id;
        } else {
            $studentId = !empty($model->studentId) ? $model->studentId : null;
        }
       	$locationId = Yii::$app->session->get('location_id');
        $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
        $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
        $lessonDuration = explode(':', $model->fullDuration);
        $date = new \DateTime($model->date);
        $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
        $lessonFullEndTime = $date->format('H:i:s');
        $date->modify('-1 second');
        $lessonEndTime = $date->format('H:i:s');
        $query = Lesson::find()
			->studentLessons($locationId, $studentId)
			->andWhere(['NOT', ['lesson.id' => $model->id]])
			->isConfirmed()
			->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
			->all();

        if((!empty($model->vacationId) || empty($model->vacationId)) && !empty($studentLessons)) {
            foreach($studentLessons as $studentLesson) {
                if(new \DateTime($model->date) == new \DateTime($studentLesson->date) && (int) $studentLesson->status === Lesson::STATUS_SCHEDULED) {
                    continue;
                }
                $conflictedLessonIds[] = $model->id;
            }
            if(!empty($conflictedLessonIds)) {
                $this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
            }
        }
		$vacations = Vacation::find()
			->andWhere(['enrolmentId' => $model->course->enrolment->id])
			->andWhere(['>=', 'DATE(fromDate)', (new \DateTime())->format('Y-m-d')])
			->all();
		foreach($vacations as $vacation) {
			$date = Carbon::parse($model->date); 
			$start = Carbon::parse($vacation->fromDate);
			$end = Carbon::parse($vacation->toDate); 
			$diff = $start->diff($end);
			$interval = CarbonInterval::year($diff->y)->months($diff->m)->days($diff->d);
			$period = Period::createFromDuration($start, $interval);
			$result = $period->contains($date);
			if(!empty($result)) {
                $this->addError($model,$attribute, 'Lesson date/time conflicts with student\'s vacation');
            }
		}
    }
}
