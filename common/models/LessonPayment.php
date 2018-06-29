<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;


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
            [['isDeleted'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
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
    public static function find()
    {
        return new \common\models\query\LessonPaymentQuery(get_called_class());
    }
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId']);
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
