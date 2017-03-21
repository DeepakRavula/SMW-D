<?php

namespace common\models;

use Yii;
use common\models\query\StudentQuery;
use common\models\Student;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\TimelineEventLink;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class StudentLog extends Student
{
	public static function tableName()
    {
        return '{{%student}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 30],
            [[ 'status'], 'integer'],
            [['birth_date'], 'date', 'format' => 'php:d-m-Y'],
            [['customer_id'], 'safe'],
        ];
    }


	public static function create($model) {
		$student = Student::find(['id' => $model->id])->asArray()->one();
		$user = User::findOne(['id' => Yii::$app->user->id]);
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'category' => 'student',
			'event' => 'create',
			'data' => $student,
			'message' => $user->publicIdentity . ' created a new student {{' . $model->fullName . '}}',
		]));
		$timelineEventLink = new TimelineEventLink();
		$timelineEventLink->timelineEventId = $timelineEvent->id;
		$timelineEventLink->index = $model->fullName;
		$timelineEventLink->baseUrl = Yii::$app->homeUrl;
		$timelineEventLink->path = Url::to(['/student/view', 'id' => $model->id]);
		$timelineEventLink->save();
	}
}
