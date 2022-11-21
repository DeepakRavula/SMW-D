<?php

namespace common\models;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "holiday".
 *
 * @property string $id
 * @property string $date
 */
class Holiday extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'holiday';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'description', 'isDeleted'], 'safe'],
            [['description'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
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
     *
     * @return \common\models\query\HolidayQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\HolidayQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        $this->isDeleted =  false;
         $holidayDate = Yii::$app->formatter->asDate($this->date);
        $this->date = (new \DateTime($holidayDate))->format('Y-m-d');

        return parent::beforeSave($insert);
    }
}
