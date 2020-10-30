<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "enrolment_program_rate".
 *
 * @property integer $id
 * @property integer $enrolmentId
 * @property string $startDate
 * @property string $endDate
 * @property string $programRate
 */
class CourseProgramRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_program_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'startDate', 'endDate', 'programRate'], 'required'],
            [['courseId'], 'integer'],
            [['programRate'], 'number'],
            [['startDate', 'endDate'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'programRate' => 'Program Rate',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CourseProgramRateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseProgramRateQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->applyFullDiscount)) {
                $this->applyFullDiscount = false;
            }
        }
        return parent::beforeSave($insert);
    }
}
