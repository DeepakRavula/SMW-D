<?php

namespace common\models\query;


class ProformaInvoiceQuery extends \yii\db\ActiveQuery
{

    public function all($db = null)
    {
        return parent::all($db);
    }

   
    public function one($db = null)
    {
        return parent::one($db);
    }
    public function location($locationId)
    {
      
               $this->andWhere(['proforma_invoice.locationId' => $locationId]);
        

        return $this;
    }

    public function notDeleted()
    {
        $this->andWhere(['proforma_invoice.isDeleted' => false]);
        return $this;
    }

}
