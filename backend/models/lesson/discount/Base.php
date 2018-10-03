<?php

namespace backend\models\lesson\discount;

use yii\helpers\VarDumper;
use yii\base\Exception;
use yii\base\Model;
use common\models\discount\LessonDiscount;

/**
 * Create user form.
 */
class Base extends Model
{
    public $model;
    public $lessonId;
    public $type;
    public $valueType;
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'max' => 100]
        ];
    }
    
    /**
     * @return User
     */
    public function getDiscountModel()
    {
        $lessonDiscount = LessonDiscount::find()
                ->andWhere(['lessonId' => $this->lessonId,
                    'type' => $this->type])
                ->one();

        return !empty($lessonDiscount) ? $lessonDiscount : new LessonDiscount();
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
            $lessonDiscount = $this->getDiscountModel();
            $lessonDiscount->lessonId = $this->lessonId;
            if (round($lessonDiscount->value, 2) !== round($this->value, 2)) {
                $lessonDiscount->value = $this->value;
            }
            $lessonDiscount->valueType = (int) $this->valueType;
            $lessonDiscount->type = $this->type;
            if (!$lessonDiscount->save()) {
                Yii::error('Line item discount error: '.VarDumper::dumpAsString($lessonDiscount->getErrors()));
            }
            return !$lessonDiscount->hasErrors();
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
