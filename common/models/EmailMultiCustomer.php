<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Lesson;
use common\components\validators\lesson\conflict\ClassroomValidator;

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
class EmailMultiCustomer extends Model
{
    const SCENARIO_SEND_EMAIL_MULTICUSTOMER = 'send-email-multi-customer';

    public $lessonIds;
        
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonIds'], 'safe'],
        ];
    }
 
}
