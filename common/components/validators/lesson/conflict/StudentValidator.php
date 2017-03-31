<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeExclusive;
use common\models\Lesson;

class StudentValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		if($model->course->program->isPrivate()) {
			$studentId = $model->course->enrolment->student->id; 
		} else {
			$studentId = !empty($model->studentId) ? $model->studentId : null;
		}
       	$locationId = Yii::$app->session->get('location_id');
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
		$lessonDuration = explode(':', $model->duration);
		$date = new \DateTime($model->date);
		$date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$lessonEndTime = $date->format('H:i:s');
		$studentLessons = Lesson::find()
			->studentLessons($locationId, $studentId)
			->andWhere(['DATE(date)' => $lessonDate])
            ->andWhere(['OR', 
                [
                    'between', 'TIME(DATE)', $lessonStartTime, $lessonEndTime
                ],
                [
                    'between', 'ADDTIME(TIME(DATE),lesson.duration)', $lessonStartTime, $lessonEndTime
                ],
                [
                    'AND',
                    [
                        '<=', 'TIME(DATE)', $lessonStartTime
                    ],
                    [
                        '>=', 'ADDTIME(TIME(DATE),lesson.duration)', $lessonEndTime
                    ]
                    
                ]
            ])
			->all();		
        if ((!empty($studentLessons))) {
            $this->addError($model,$attribute, 'Lesson time conflicts with student\'s another lesson');
        }
    }
}