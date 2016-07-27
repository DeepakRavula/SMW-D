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

	const PAYMENT_METHOD_CASH = 1;
	const PAYMENT_METHOD_CREDIT_CARD = 2;
	const PAYMENT_METHOD_CHEQUE = 3;
	const PAYMENT_METHOD_ACCOUNT = 4;

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
            'status' => 'Status',
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
  
	public function status($data)
    {
		$status = null;

        switch($data->status){
			case Invoice::STATUS_OWING:
				$status = 'Owing';
			break;
			case Invoice::STATUS_PAID:
				$status = 'Paid';
			break;
			case Invoice::STATUS_CREDIT:
				$status = 'Credited';
			break;
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

	public static function paymentMethods()
    {
        return [
            self::PAYMENT_METHOD_CASH => Yii::t('common', 'Cash'),
            self::PAYMENT_METHOD_CREDIT_CARD => Yii::t('common', 'Credit Card'),
            self::PAYMENT_METHOD_CHEQUE => Yii::t('common', 'Cheque'),
            self::PAYMENT_METHOD_ACCOUNT => Yii::t('common', 'Account'),
        ];
    }
}
