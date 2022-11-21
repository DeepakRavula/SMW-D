<?php

namespace common\models;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

use Yii;

/**
 * This is the model class for table "referral_sources".
 *
 * @property int $id
 * @property string $source_name
 */
class LocationPaymentPreference extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public static function tableName()
    {
        return 'location_payment_preference';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['locationId', 'isPreferredPaymentEnabled', 'createdOn', 'updatedOn', 'createdByUserId', 'updatedByUserId'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId',
            ],
        ];
    }
    
    public static function find()
    {
        return new \common\models\query\LocationPaymentPreferenceQuery(get_called_class());
    }
}
