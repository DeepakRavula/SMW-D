<?php

namespace common\models;

use common\models\query\ReferralSourceQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "referral_sources".
 *
 * @property int $id
 * @property string $source_name
 */
class ReferralSource extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const REFERRAL_SOURCE_OTHER = '4';
    
    public static function tableName()
    {
        return 'referral_sources';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    public static function find()
    {
        return new \common\models\query\ReferralSourceQuery(get_called_class());
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
            'name' => 'Name',
        ];
    }

    public function isOther()
    {
        return $this->id == self::REFERRAL_SOURCE_OTHER;
    }
}
