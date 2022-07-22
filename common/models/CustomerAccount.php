<?php

namespace common\models;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\UserLocation;
use common\models\User;

use Yii;

/**
 * This is the model class for table "customer_account_info".
 *
 * @property string $description
 * @property integer $invoiceId
 * @property string $date
 * @property string $debit
 * @property string $credit
 */
class CustomerAccount extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'balance', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        ];
    }

    public function validateUser($attributes)
    {
        $user = User::findOne(['id' => $this->customerId]);
        if (!$user->isCustomer()) {
            $this->addError($attributes, 'Unable to add this user on customer account because user is not a customer');
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customerId' => 'Customer ID',
            'balance' => 'Balance',
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
     * @return \common\models\query\CustomerAccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CustomerAccountQuery(get_called_class());
    }

    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'customerId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }
}
