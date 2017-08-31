<?php

namespace common\models;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property int $province_id
 */
class LessonPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%lesson_payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'paymentId', 'enrolmentId'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           
        ];
    }
	public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
	public function getCreditUsage()
    {
        return $this->hasOne(CreditUsage::className(), ['credit_payment_id' => 'paymentId']);
    }
	public function getCredit()
    {
        $payment = Payment::findOne(['id' => $this->paymentId]);
		
        return $payment->amount;
    }
}
