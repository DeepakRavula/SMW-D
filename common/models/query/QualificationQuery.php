<?php

namespace common\models\query;

use common\models\Payment;
use common\models\Invoice;
use yii\db\ActiveQuery;

/**
 * Class PaymentQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class QualificationQuery extends ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Payment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function notDeleted()
    {
        return $this->andWhere(['qualification.isDeleted' => false]);
    }

    public function location($locationId)
    {
        return $this->joinWith('teacherLocation')
            ->andWhere(['user_location.location_id' => $locationId]);
    }
}
