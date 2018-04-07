<?php

namespace backend\models\discount;

use yii\helpers\VarDumper;
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
            [['value'], 'number', 'min' => 0, 'max' => 100]
        ];
    }
    
    /**
     * @return User
     */
    public function getDiscountModel()
    {
        $lineitemDiscount = InvoiceLineItemDiscount::find()
                ->andWhere(['invoiceLineItemId' => $this->invoiceLineItemId,
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
            if (round($lineItemDiscount->value, 2) !== round($this->value, 2)) {
                $lineItemDiscount->value = $this->value;
            }
            $lineItemDiscount->valueType = (int) $this->valueType;
            $lineItemDiscount->type = $this->type;
            if (!$lineItemDiscount->save()) {
                Yii::error('Line item discount error: '.VarDumper::dumpAsString($lineItemDiscount->getErrors()));
            }
            return !$lineItemDiscount->hasErrors();
        }

        return null;
    }
    
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name]) && $values[$name] != null && (!empty($values[$name]) || $values[$name] == 0)) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }
}
