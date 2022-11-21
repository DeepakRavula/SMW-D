<?php

namespace backend\models\lesson\discount;

use backend\models\lesson\discount\Base;
use common\models\discount\LessonDiscount;

/**
 * Create user form.
 */
class EnrolmentLessonDiscount extends Base
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
            $this->model = new EnrolmentLessonDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
        $this->type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
    }
}
