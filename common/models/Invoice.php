<?php

namespace common\models;

use Yii;
use common\models\query\InvoiceQuery;
use common\models\InvoiceLineItem;

/**
 * This is the model class for table "invoice".
 *
 * @property integer $id
 * @property integer $lesson_id
 * @property integer $type
 * @property string $amount
 * @property string $date
 * @property integer $status
 */
class Invoice extends \yii\db\ActiveRecord
{
	const STATUS_PAID = 1; 
	const STATUS_OWING = 2;
	const STATUS_CREDIT = 3;

	const TYPE_PRO_FORMA_INVOICE = 1;
	const TYPE_INVOICE = 2;

	public $customer_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['user_id','required'],
			[['type','notes','internal_notes'],'safe']
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'invoice_number' => 'Invoice Number',
            'date' => 'Date',
			'notes' => 'Printed Notes',
			'internal_notes' => 'Internal Notes',
			'type' => 'Type'
        ];
    }

    /**
     * @inheritdoc
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }

    public function getLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id']);
    }

	public function getPayment()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'user_id']);
    }
	
	public function getAllocations()
    {
        return $this->hasMany(Allocation::className(), ['invoice_id' => 'id']);
    }
	
	public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
	
	public function getInvoicePaymentTotal(){
		$invoiceAmounts = Payment::find()
				->joinWith('invoicePayment ip')
				->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
				->all();
		
		$sumOfInvoicePayment = 0;
		if(! empty($invoiceAmounts)){
			foreach($invoiceAmounts as $invoiceAmount){
				$sumOfInvoicePayment += $invoiceAmount->amount; 
			}
		}
		return $sumOfInvoicePayment;
	}
	
	public function getInvoiceBalance(){
		$balance = $this->total - $this->invoicePaymentTotal;
		return $balance;
	}
	
	public function getStatus()
    {
		$status = null;	
		if((int) $this->total === (int) $this->invoicePaymentTotal){
			$status = 'Paid'; 
		}
		elseif($this->total > $this->invoicePaymentTotal){
			$status = 'Owing'; 
		}else{
			$status = 'Paid'; 
			if($this->type == self::TYPE_INVOICE) {
				$status = 'Credit'; 	
			}
		}
		
		return $status;
    }
   
    public static function lastInvoice($location_id){
        return $query = Invoice::find()->alias('i')
            ->joinwith(['lineItems' => function($query) use($location_id){
                $query->joinWith(['lesson' => function($query) use($location_id){
                        $query->joinWith(['enrolment e' => function($query) use($location_id){
                        	$query->where(['e.location_id' => $location_id]);
						}]);
                }]);
            }])
            ->orderBy(['i.id' => SORT_DESC])
            ->one();
    }
}
