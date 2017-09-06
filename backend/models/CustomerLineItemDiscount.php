<?php

namespace backend\models;

use common\models\User;
use common\models\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class CustomerLineItemDiscount extends Discount
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
            ['value', 'safe', 'on' => self::SCENARIO_ON_INVOICE],
            [['invoiceLineItemId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'min' => 0, 'max' => 100],
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
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new CustomerLineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $this->type = InvoiceLineItemDiscount::TYPE_CUSTOMER;
    }
}
