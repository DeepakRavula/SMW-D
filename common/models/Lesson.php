<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $enrolmentId
 * @property string $teacherId
 * @property string $date
 * @property integer $status
 * @property integer $isDeleted
 */
class Lesson extends \yii\db\ActiveRecord
{

	const TYPE_PRIVATE_LESSON = 1;
	const TYPE_GROUP_LESSON = 2;
	const STATUS_DRAFTED = 1;
	const STATUS_SCHEDULED = 2;
	const STATUS_COMPLETED = 3;
	const STATUS_CANCELED = 4;

	public $programId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'teacherId', 'status', 'isDeleted'], 'required'],
            [['enrolmentId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date', 'programId', 'notes'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programId' => 'Program Name',
            'enrolmentId' => 'Enrolment ID',
            'teacherId' => 'Teacher Name',
            'date' => 'Date',
            'status' => 'Status',
            'isDeleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\LessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\LessonQuery(get_called_class());
    }

	public function getEnrolment() {
		return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
	}

	public function getCourse() {
		return $this->hasOne(Course::className(), ['id' => 'courseId'])
			->viaTable('enrolment',['id' => 'enrolmentId']);
	}

	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
			->viaTable('invoice_line_item', ['item_id' => 'id'])
			->onCondition(['invoice.type' => Invoice::TYPE_INVOICE]);
	}

	public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['item_id' => 'id'])
				->where(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON]);
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }
	
	public function getStatus(){
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
		$currentDate = new \DateTime();
			$status = null;
			switch ($this->status) {
				case Lesson::STATUS_SCHEDULED:
					if ($lessonDate >= $currentDate) {
						$status = 'Scheduled';
					} else {
						$status = 'Completed';
					}
					break;
				case Lesson::STATUS_COMPLETED;
					$status = 'Completed';
					break;
				case Lesson::STATUS_CANCELED:
					$status = 'Canceled';
					break;
			}
			
		return $status;
	}
	
	public static function lessonStatuses() {
		return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
			self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
            self::STATUS_CANCELED => Yii::t('common', 'Canceled'),
		];
	}
}
