<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_account".
 *
 * @property string $id
 * @property string $foreignKeyId
 * @property integer $type
 * @property integer $actionType
 * @property double $amount
 * @property double $credit
 * @property double $debit
 * @property double $balance
 */
class CustomerAccount extends \yii\db\ActiveRecord
{
    const TYPE_INVOICE = 1;
    const TYPE_PAYMENT = 2;
    const ACTION_TYPE_CREATE = 1;
    const ACTION_TYPE_UPDATE = 2;
    const ACTION_TYPE_DELETE = 3;

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
            [['foreignKeyId', 'type', 'actionType', 'amount', 'balance'], 'required'],
            [['foreignKeyId', 'type', 'actionType'], 'integer'],
            [['amount', 'credit', 'debit', 'balance'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreignKeyId' => 'Foreign Key',
            'type' => 'Type',
            'actionType' => 'Action Type',
            'amount' => 'Amount',
            'credit' => 'Credit',
            'debit' => 'Debit',
            'balance' => 'Balance',
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

    public function getActionUser()
    {
        return $this->hasOne(User::className(), ['id' => 'actionUserId']);
    }

    public function getAccountActionType()
    {
        switch($this->actionType) {
            case self::ACTION_TYPE_CREATE :
                $actionType = 'created';
            break;
            case self::ACTION_TYPE_UPDATE :
                $actionType = 'updated';
            break;
            case self::ACTION_TYPE_DELETE :
                $actionType = 'deleted';
            break;
        }
        return $actionType;
    }

    public function getAccountType()
    {
        switch($this->type) {
            case self::TYPE_INVOICE :
                $type = 'Invoice';
            break;
            case self::TYPE_PAYMENT :
                $type = 'Payment';
            break;
        }
        return $type;
    }
}
