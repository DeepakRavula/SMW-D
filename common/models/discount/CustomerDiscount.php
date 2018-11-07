<?php

namespace common\models\discount;

use common\models\User;
use common\models\query\CustomerDiscountQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "customer_discount".
 *
 * @property string $id
 * @property string $customerId
 * @property double $value
 */
class CustomerDiscount extends \yii\db\ActiveRecord
{
    public $userName;

    /**
     * @inheritdoc
     */
    const EVENT_CREATE = 'event-create';
    const EVENT_EDIT = 'event-edit';
    const CONSOLE_USER_ID = 727;
    public static function tableName()
    {
        return 'customer_discount';
    }

    public static function find()
    {
        return new CustomerDiscountQuery(get_called_class(), parent::find()->andWhere(['customer_discount.isDeleted' => false]));
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
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'value'], 'required'],
            [['customerId'], 'integer'],
            [['isDeleted'], 'safe'],
            [['value'], 'number', 'max' => 100.00, 'message' => 'Invalid discount'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer ID',
            'value' => 'Value',
        ];
    }
    public function beforeSave($insert)
    {
        $this->isDeleted = false;
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $this->trigger(self::EVENT_EDIT);
        }
        $this->trigger(self::EVENT_CREATE);
        return parent::afterSave($insert, $changedAttributes);
    }
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }
}
