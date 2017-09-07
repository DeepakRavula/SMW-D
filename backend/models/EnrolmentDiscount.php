<?php

namespace backend\models;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
/**
 * Create user form.
 */
class EnrolmentDiscount extends Model
{
    public $model;
    public $enrolmentId;
    public $type;
    public $discountType;
    public $discount;

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
     * @return User
     */
    public function getDiscountModel()
    {
        $enrolmentDiscount = \common\models\EnrolmentDiscount::find()
                ->where(['enrolmentId' => $this->enrolmentId,
                    'type' => $this->type])
                ->one();

        return !empty($enrolmentDiscount) ? $enrolmentDiscount : new \common\models\EnrolmentDiscount();
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
            $enrolmentDiscount = $this->getDiscountModel();
            $enrolmentDiscount->enrolmentId = $this->enrolmentId;
            $enrolmentDiscount->discount = $this->discount;
            $enrolmentDiscount->discountType = $this->discountType;
            $enrolmentDiscount->type = $this->type;
            if (!$enrolmentDiscount->save()) {
                throw new Exception('Model not saved');
            }
            return !$enrolmentDiscount->hasErrors();
        }

        return null;
    }
}
