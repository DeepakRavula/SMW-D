<?php

namespace backend\models\discount;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use common\models\discount\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class InvoiceDiscount extends Model
{
    public $model;
    public $invoiceLineItemId;
    public $type;
    public $valueType;
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'min' => 0, 'max' => 100],
        ];
    }
    
    /**
     * @return User
     */
    public function getDiscountModel()
    {
        $lineitemDiscount = InvoiceLineItemDiscount::find()
                ->where(['invoiceLineItemId' => $this->invoiceLineItemId,
                    'type' => $this->type])
                ->one();

        return !empty($lineitemDiscount) ? $lineitemDiscount : new InvoiceLineItemDiscount();
    }
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     *
     * @throws Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $lineItemDiscount = $this->getDiscountModel();
            $lineItemDiscount->invoiceLineItemId = $this->invoiceLineItemId;
            $lineItemDiscount->value = $this->value;
            $lineItemDiscount->valueType = $this->valueType;
            $lineItemDiscount->type = $this->type;
            if (!$lineItemDiscount->save()) {
                throw new Exception('Model not saved');
            }
            return !$lineItemDiscount->hasErrors();
        }

        return null;
    }
}
