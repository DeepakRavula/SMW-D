<?php

namespace backend\models\lesson\discount;

use common\models\discount\LessonDiscount;
use backend\models\lesson\discount\Base;

/**
 * Create user form.
 */
class CustomerLessonDiscount extends Base
{
    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model, $value = null)
    {
        $this->value = $model->value;
        if ($value) {
            $this->value = null;
        }
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new CustomerLessonDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
        $this->type = LessonDiscount::TYPE_CUSTOMER;
    }
}
