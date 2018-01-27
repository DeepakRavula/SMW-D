<?php
namespace common\components\validators\lesson\conflict;

use Yii;
use common\models\Location;
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
        if (!empty($model->duration)) {
            if ($model->isExtra()) {
                $studentId = $model->studentId;
            } elseif ($model->course->program->isPrivate()) {
            $studentId = $model->enrolment->student->id;
            } else {
                $studentId = !empty($model->studentId) ? $model->studentId : null;
            }
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $lessonDate = (new \DateTime($model->date))->format('Y-m-d');
            $lessonStartTime = (new \DateTime($model->date))->format('H:i:s');
            $lessonDuration = explode(':', $model->fullDuration);
            $date = new \DateTime($model->date);
            $date->add(new \DateInterval('PT' . $lessonDuration[0] . 'H' . $lessonDuration[1] . 'M'));
            $date->modify('-1 second');
            $lessonEndTime = $date->format('H:i:s');
            $studentLessons = Lesson::find()
                ->studentLessons($locationId, $studentId)
                ->andWhere(['NOT', ['lesson.id' => $model->id]])
                ->isConfirmed()
                ->overlap($lessonDate, $lessonStartTime, $lessonEndTime)
                ->all();

            if ($studentLessons) {
                $this->addError($model, $attribute, 'Lesson time conflicts with student\'s another lesson');
            }
        }
        if ($model->course && $model->enrolment) {
            $vacations = Vacation::find()
                    ->andWhere(['enrolmentId' => $model->enrolment->id])
                    ->andWhere(['>=', 'DATE(fromDate)', (new \DateTime())->format('Y-m-d')])
                    ->andWhere(['isDeleted' => false])
                    ->all();
            foreach ($vacations as $vacation) {
                $date = Carbon::parse($model->date);
                $start = Carbon::parse($vacation->fromDate);
                $end = Carbon::parse($vacation->toDate);
                $diff = $start->diff($end);
                $interval = CarbonInterval::year($diff->y)->months($diff->m)->days($diff->d);
                $period = Period::createFromDuration($start, $interval);
                $result = $period->contains($date);
                if (!empty($result)) {
                    $this->addError($model, $attribute, 'Lesson date/time conflicts with student\'s vacation');
                }
            }
        }
    }
}
