<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "professional_development_day".
 *
 * @property string $id
 * @property string $date
 */
class ProfessionalDevelopmentDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'professional_development_day';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ProfessionalDevelopmentDayQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProfessionalDevelopmentDayQuery(get_called_class());
    }
}
