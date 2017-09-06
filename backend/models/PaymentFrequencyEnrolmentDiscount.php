<?php

namespace backend\models;

use common\models\User;
use backend\models\EnrolmentDiscount;
/**
 * Create user form.
 */
class PaymentFrequencyEnrolmentDiscount extends EnrolmentDiscount
{
    private $model;
    public $enrolmentId;
    public $type;
    public $discountType;
    public $discount;

    public function init()
    {
        $this->discountType = \common\models\EnrolmentDiscount::VALUE_TYPE_PERCENTAGE;
        $this->type = \common\models\EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'discountType', 'type'], 'integer'],
            [['discount'], 'number', 'min' => 0, 'max' => 100],
        ];
    }

    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->enrolmentId = $model->enrolmentId;
        $this->discount = $model->discount;
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new PaymentFrequencyEnrolmentDiscount();
        }

        return $this->model;
    }
}
