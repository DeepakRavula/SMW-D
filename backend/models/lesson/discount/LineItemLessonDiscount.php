<?php

namespace backend\models\lesson\discount;

use backend\models\lesson\discount\Base;
use common\models\discount\LessonDiscount;

/**
 * Create user form.
 */
class LineItemLessonDiscount extends Base
{
    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model, $value = null)
    {
        $this->value = $model->value;
        $this->valueType = $model->valueType;
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
            $this->model = new LineItemLessonDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->type = LessonDiscount::TYPE_LINE_ITEM;
    }
}
