<?php

namespace backend\models;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use common\models\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class CustomerLineItemDiscount extends Model
{
    private $model;
    public $invoiceLineItemId;
    public $type;
    public $valueType;
    public $value;

    const SCENARIO_ON_INVOICE = 'invoice';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'required'],
            ['value', 'safe', 'on' => self::SCENARIO_ON_INVOICE],
            [['invoiceLineItemId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        
            
        ];
    }

    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->invoiceLineItemId = $model->invoiceLineItemId;
        $this->value = $model->value;
        $this->model = $model;
        return $this->model;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new InvoiceLineItemDiscount();
        }

        return $this->model;
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
            $lineItemDiscount = new InvoiceLineItemDiscount();
            $lineItemDiscount->invoiceLineItemId = $this->invoiceLineItemId;
            $lineItemDiscount->value = $this->value;
            $lineItemDiscount->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
            $lineItemDiscount->type = InvoiceLineItemDiscount::TYPE_CUSTOMER;
            
            if (!$lineItemDiscount->save()) {
                throw new Exception('Model not saved');
            }
            return !$lineItemDiscount->hasErrors();
        }

        return null;
    }
}
