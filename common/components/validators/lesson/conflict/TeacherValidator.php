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
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
		$lessonDuration = explode(':', $model->duration);
		$date = new \DateTime($model->date);
		$date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$lessonEndTime = $date->format('H:i:s');
		$conflictedLessonsResults = [];
        foreach ($teacherLessons as $teacherLesson) {
			if(!empty($model->vacationId) && new \DateTime($teacherLesson->date) == new \DateTime($model->date) && $teacherLesson->isScheduled()) {
				continue;
			}
			$teacherLessonDate = (new \DateTime($teacherLesson->date))->format('Y-m-d');
			$teacherLessonStartTime = (new \DateTime($teacherLesson->date))->format('H:i:s');
			$duration = explode(':', $teacherLesson->duration);
			$date = new \DateTime($teacherLesson->date);
			$date->add(new \DateInterval('PT' . $duration[0] . 'H' . $duration[1] . 'M'));	
			$teacherLessonEndTime = $date->format('H:i:s');
			if($lessonDate == $teacherLessonDate && ($lessonStartTime >= $teacherLessonStartTime && $lessonEndTime <= $teacherLessonEndTime)) {
        		$conflictedLessonsResults = $teacherLesson->id;
        	}
        if ((!empty($conflictedLessonsResults))) {
            $this->addError($model,$attribute, 'Teacher occupied with another lesson');
        }
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