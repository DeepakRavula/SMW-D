<?php

namespace backend\models;

use common\models\User;
use backend\models\EnrolmentDiscount;
/**
 * Create user form.
 */
class MultiEnrolmentDiscount extends EnrolmentDiscount
{
    private $model;
    public $enrolmentId;
    public $type;
    public $discountType;
    public $discount;

    public function init()
    {
        $this->discountType = \common\models\EnrolmentDiscount::VALUE_TYPE_DOLOR;
        $this->type = \common\models\EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
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
            $this->model = new MultiEnrolmentDiscount();
        }

        return $this->model;
    }
}
