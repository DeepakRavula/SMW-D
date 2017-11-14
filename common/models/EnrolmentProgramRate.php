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
class EnrolmentProgramRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment_program_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'startDate', 'endDate', 'programRate'], 'required'],
            [['enrolmentId'], 'integer'],
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
            'enrolmentId' => 'Enrolment ID',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'programRate' => 'Program Rate',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\EnrolmentProgramRateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\EnrolmentProgramRateQuery(get_called_class());
    }
}
