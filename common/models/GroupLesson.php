<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\discount\LessonDiscount;
use backend\models\lesson\discount\CustomerLessonDiscount;
use backend\models\lesson\discount\LineItemLessonDiscount;
use backend\models\lesson\discount\EnrolmentLessonDiscount;
use backend\models\lesson\discount\PaymentFrequencyLessonDiscount;
use yii\base\Model;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $teacherId
 * @property string $date
 * @property int $status
 * @property int $isDeleted
 */
class GroupLesson extends Model
{
    public $lessonId;
    public $enrolmentId;

    public function rules()
    {
        return [
            [['lessonId', 'enrolmentId'], 'safe']
        ];
    }
    
    public function loadDiscount()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $discount = LessonDiscount::find()
            ->groupDiscount()
            ->andWhere(['lessonId' => $this->lessonId, 'enrolmentId' => $this->enrolmentId])
            ->one();
        if (!$discount) {
            $discount = new LessonDiscount();
            $discount->lessonId = $lesson->id;
            $discount->enrolmentId = $this->enrolmentId;
            $discount->type = LessonDiscount::TYPE_GROUP;
            $discount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
        }
        
        return $discount;
    }
}
