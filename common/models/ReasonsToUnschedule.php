<?php

namespace common\models;

use common\models\query\ReasonsToUnscheduleQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "reason_to_unschedule".
 *
 * @property int $id
 * @property string $reason
 */
class ReasonsToUnschedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const REASONS_TO_UNSCHEDULE_OTHER = '4';
    
    public static function tableName()
    {
        return 'reasons_to_unschedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reason'], 'string', 'max' => 255],
        ];
    }

    public static function find()
    {
        return new \common\models\query\ReasonToUnscheduleQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true,
            ],
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reason' => 'Reason',
        ];
    }

    public function isOther()
    {
        return $this->id == self::REASONS_TO_UNSCHEDULE_OTHER;
    }
}
