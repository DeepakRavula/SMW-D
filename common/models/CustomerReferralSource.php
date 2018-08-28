<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\ReferralSource;

/**
 * This is the model class for table "customer_referral_sources".
 *
 * @property int $id
 * @property int $userId
 * @property int $referralSourceId
 * @property string $description
 * @property string $createdOn
 * @property string $updatedOn
 * @property int $createdByUserId
 * @property int $updatedByUserId
 */
class CustomerReferralSource extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_referral_sources';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'referralSourceId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn'], 'safe'],
            [['createdByUserId', 'updatedByUserId'], 'safe'],
            [['description'], 'string', 'max' => 255],
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
                'updatedByAttribute' => 'updatedByUserId'
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
            'userId' => 'User ID',
            'referralSourceId' => 'Referral Source ID',
            'description' => 'Description',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }
    
    public function getReferralSource()
    {
        return $this->hasOne(ReferralSource::className(), ['id' => 'referralSourceId']);
    }

    public function setModel($model)
    {
        $this->referralSourceId = $model->referralSourceId;
        $this->description = $model->description;
        return $this;
    }
}
