<?php

namespace backend\models\discount;

use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use common\models\Enrolment;

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
            [['discount'], 'number', 'max' => 100],
        ];
    }
    
    /**
     * @return User
     */
    public function getDiscountModel()
    {
        $enrolmentDiscount = \common\models\discount\EnrolmentDiscount::find()
                ->andWhere(['enrolmentId' => $this->enrolmentId,
                    'type' => $this->type])
                ->one();

        return !empty($enrolmentDiscount) ? $enrolmentDiscount : new \common\models\discount\EnrolmentDiscount();
    }
    public function getEnrolment()
    {
        return $this->hasMany(Enrolment::className(), ['enrolmentId' => 'id']);
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
