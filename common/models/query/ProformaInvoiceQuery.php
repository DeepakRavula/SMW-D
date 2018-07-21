<?php

namespace common\models\query;
use common\models\ProformaInvoice;


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
    public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'DATE(proformainvoice.dueDate)', $fromDate, $toDate]);
    }
    public function unpaid()
    {
        return $this->andFilterWhere([
            'proforma_invoice.status' => ProformaInvoice::STATUS_UNPAID,
        ]);
    }
    public function paid()
    {
        return $this->andFilterWhere([
            'proforma_invoice.status' => ProformaInvoice::STATUS_UNPAID,
        ]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['proforma_invoice.isDeleted' => false]);
    }

}
