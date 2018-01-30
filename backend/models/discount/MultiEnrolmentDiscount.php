<?php

namespace backend\models\discount;

use common\models\User;
use backend\models\discount\EnrolmentDiscount;

/**
 * Create user form.
 */
class MultiEnrolmentDiscount extends EnrolmentDiscount
{
    public function init()
    {
        $this->discountType = \common\models\discount\EnrolmentDiscount::VALUE_TYPE_DOLOR;
        $this->type = \common\models\discount\EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
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
