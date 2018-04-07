<?php

namespace common\components\validators\vacation;

use yii\validators\Validator;
use Yii;
use common\models\Vacation;

class EnrolmentDateValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $locationId        = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $dateRange         = $model->dateRange;
        list($fromDate, $toDate) = explode(' - ', $dateRange);
        $fromDate          = \DateTime::createFromFormat('M d,Y', $fromDate)->format('Y-m-d');
        $toDate            = \DateTime::createFromFormat('M d,Y', $toDate)->format('Y-m-d');
        $enrolmentFromDate = $model->enrolment->course->startDate;
        $enrolmentToDate   = $model->enrolment->course->endDate;
        $enrolmentFromDate = (new \DateTime($enrolmentFromDate))->format('Y-m-d');
        $enrolmentToDate   = (new \DateTime($enrolmentToDate))->format('Y-m-d');
        if (($fromDate < $enrolmentFromDate && $toDate < $enrolmentToDate) || ($fromDate
            > $enrolmentFromDate && $toDate > $enrolmentToDate)) {
            return $this->addError(
                $model,
                $attribute,
                    'Vacation Can Only be created between Enrolment'
            );
        }

        $vacation = Vacation::find()
            ->andWhere(['AND', ['<=', 'fromDate', $fromDate], ['>=', 'toDate', $fromDate]])
            ->orWhere(['AND', ['<=', 'fromDate', $toDate], ['>=', 'toDate', $toDate]])
            ->orWhere(['AND', ['>', 'fromDate', $fromDate], ['<', 'toDate', $toDate]])
            ->andWhere(['enrolmentId' => $model->enrolmentId])
            ->count();

        if (!empty($vacation)) {
            return $this->addError(
                $model,
                $attribute,
                    'Avoid vacation Replication'
            );
        }
    }
}
