<?php

namespace common\models\query;
use common\models\Invoice;
/**
 * This is the ActiveQuery class for [[Invoice]].
 *
 * @see Invoice
 */
class InvoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Invoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Invoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
	public function student($id) {
		$this->joinWith(['lineItems li'=>function($query) use($id){
			$query->joinWith(['lesson l'=>function($query) use($id){	
				$query->joinWith(['enrolment e'=>function($query) use($id){
					$query->joinWith('student s')
						->where(['s.customer_id' => $id]);
					}]);
				}]);
			}]);
		return $this;
	}

	public function invoiceCredit($userId){
		$this->where([
			'user_id' => $userId,
			'type' => Invoice::TYPE_INVOICE,
		])
		->andWhere(['<', 'balance', 0]);
	
		return $this;	
	}

	public function pendingInvoices($enrolmentId, $model) {
		$this->joinWith(['lineItems li'=>function($query) use($enrolmentId, $model){
			$query->joinWith(['lesson l'=>function($query) use($enrolmentId, $model){	
				$query->joinWith(['enrolment'=>function($query) use($enrolmentId, $model){
					$query->joinWith('student s')
						->where(['s.customer_id' => $model->customer->id, 's.id' => $model->id]);
					}])
					->where(['enrolment.id' => $enrolmentId]);
				}]);
			}]);
			
		return $this;
	}

	public function proFormaInvoiceCredits($customerId){
		$this->select(['i.id', 'i.date', 'SUM(p.amount) as credit'])
			->joinWith(['invoicePayments ip' => function($query) use($customerId){
				$query->joinWith(['payment p' => function($query) use($customerId){
				}]);
			}])
			->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $customerId])
			->groupBy('i.id');
		return $this;
	}
}
