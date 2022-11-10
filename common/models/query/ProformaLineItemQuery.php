<?php

namespace common\models\query;
use common\models\ProformaInvoice;


class ProformaLineItemQuery extends \yii\db\ActiveQuery
{

    public function all($db = null)
    {
        return parent::all($db);
    }

   
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['proforma_line_item.isDeleted' => false]);
    }

}
