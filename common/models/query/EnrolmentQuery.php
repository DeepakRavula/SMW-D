<?php

namespace common\models\query;

use common\models\Program;
use common\models\Enrolment;

/**
 * This is the ActiveQuery class for [[\common\models\Enrolment]].
 *
 * @see \common\models\Enrolment
 */
class EnrolmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Enrolment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Enrolment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['enrolment.isDeleted' => false]);
    }

    public function isConfirmed()
    {
        return $this->andWhere(['enrolment.isConfirmed' => true]);
    }

    public function isRegular()
    {
        return $this->joinWith(['course' => function ($query) {
            $query->andWhere(['course.type' => Enrolment::TYPE_REGULAR]);
        }]);
    }
    
    public function extra()
    {
        return $this->joinWith(['course' => function ($query) {
            $query->andWhere(['course.type' => Enrolment::TYPE_EXTRA]);
        }]);
    }

    public function customer($customerId)
    {
        return $this->joinWith(['student' => function ($query) use ($customerId) {
            $query->andWhere(['student.customer_id' => $customerId]);
        }]);
    }

    public function notPaymentPrefered()
    {
        return $this->joinWith(['student' => function ($query) {
            $query->joinWith(['customerPaymentPreference' => function ($query) {
                $query->andWhere(['OR', ['customer_payment_preference.id' => null], 
                    ['<', 'DATE(customer_payment_preference.expiryDate)', (new \DateTime())->format('Y-m-d')]]);
            }]);
        }]);
    }

    public function programs()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['program' => function ($query) {
            }]);
        }]);

        return $this;
    }
    
    public function privateProgram()
    {
        return $this->joinWith(['course' => function ($query) {
            $query->andWhere(['course.type' => Program::TYPE_PRIVATE_PROGRAM]);
        }]);
    }
    
    public function location($locationId)
    {
        $this->joinWith(['course' => function ($query) use ($locationId) {
            $query->andWhere(['locationId' => $locationId]);
        }]);

        return $this;
    }

    public function program($locationId, $currentDate)
    {
        $this->joinWith(['program' => function ($query) use ($locationId, $currentDate) {
            $query->andWhere(['course.locationId' => $locationId])
                ->andWhere(['>=', 'course.endDate', $currentDate->format('Y-m-d')]);
        }]);

        return $this;
    }
}
