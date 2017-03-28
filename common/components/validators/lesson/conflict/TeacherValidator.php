<?php
namespace common\components\validators\lesson\conflict;

use yii\validators\Validator;
use Yii;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeInclusive;
use common\components\intervalTree\DateRangeExclusive;
use common\models\TeacherAvailability;

class TeacherValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId = Yii::$app->session->get('location_id');
        $otherLessons = [];
        $intervals = [];	
		$teacherLessons = Lesson::find()
            ->teacherLessons($locationId, $model->teacherId)
            ->all();
        foreach ($teacherLessons as $teacherLesson) {
			if(!empty($model->vacationId) && new \DateTime($teacherLesson->date) == new \DateTime($model->date) && $teacherLesson->isScheduled()) {
				continue;
			}
            $otherLessons[] = [
                'id' => $teacherLesson->id,
                'date' => $teacherLesson->date,
                'duration' => $teacherLesson->course->duration,
            ];
        }
		foreach ($otherLessons as $otherLesson) {
            $timebits = explode(':', $otherLesson['duration']);
			$endDate = new \DateTime($otherLesson['date']);
			$endDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
            $intervals[] = new DateRangeExclusive(new \DateTime($otherLesson['date']),$endDate,null, $otherLesson['id']);
        }
		$tree = new IntervalTree($intervals);
		$duration = explode(':', $model->duration);
		$toDate = new \DateTime($model->date);
		$toDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		$searchRange = new DateRangeExclusive(new \DateTime($model->date), $toDate);
        $conflictedLessonsResults = $tree->search($searchRange);
		
        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Teacher occupied with another lesson');
        }

		$day = (new \DateTime($model->date))->format('N');
        $teacherAvailabilities = TeacherAvailability::find()
            ->joinWith(['teacher' => function ($query) use($model){
                $query->where(['user.id' => $model->teacherId]);
            }])
                ->where(['teacher_availability_day.day' => $day])
                ->all();
        $availableHours = [];
        if (empty($teacherAvailabilities)) {
            $this->addError($model,$attribute, 'Teacher is not available on '.(new \DateTime($model->date))->format('l'));
        } else {
            foreach ($teacherAvailabilities as $teacherAvailability) {
                $start = new \DateTime($teacherAvailability->from_time);
                $end = new \DateTime($teacherAvailability->to_time);
                $interval = new \DateInterval('PT15M');
                $hours = new \DatePeriod($start, $interval, $end);
                foreach ($hours as $hour) {
                    $availableHours[] = Yii::$app->formatter->asTime($hour);
                }
            }
            $lessonTime = (new \DateTime($model->date))->format('h:i A');
            if (!in_array($lessonTime, $availableHours)) {
                	$this->addError($model, $attribute, 'Please choose the lesson time within the teacher\'s availability hours');
            }
        }
    }
}