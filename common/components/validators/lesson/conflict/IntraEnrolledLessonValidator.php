<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use yii\validators\Validator;
use common\models\Lesson;
use IntervalTree\IntervalTree;
use common\components\intervalTree\DateRangeExclusive;

class IntraEnrolledLessonValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
		$lessonDuration = explode(':', $model->duration);
		$date = new \DateTime($model->date);
		$date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));	
		$lessonEndTime = $date->format('H:i:s');
		
      	$draftLessons = Lesson::find()
            ->where(['courseId' => $model->courseId, 'status' => Lesson::STATUS_DRAFTED])
            ->andWhere(['NOT', ['id' => $model->id]])
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
        
        if (!empty($draftLessons)) {
            $this->addError($model, $attribute, 'Lesson time conflicts with same enrolment lesson');
        }
    }
}