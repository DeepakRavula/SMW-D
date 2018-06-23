<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use Carbon\Carbon;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property int $payment_method_id
 * @property float $amount
 */
class OpeningBalance extends ActiveRecord
{
    public $isCredit;
    public $amount;
    public $user_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
	    ['amount','required'],
            [['amount'], 'number', 'min' => 0.1],
	    [['amount'],'number','numberPattern' => '/^\d+(.\d{1,2})?$/', 'message' => 'Only 2 decimal spaces allowed.'],
            [['isCredit', 'user_id'], 'required']
        ];
    }

    public function addOpeningBalance()
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $invoice = new Invoice();
        $invoice->user_id = $this->user_id;
        $invoice->location_id = $locationId;
        $invoice->type = Invoice::TYPE_INVOICE;
        $invoice->save();

        $invoiceLineItem = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_OPENING_BALANCE]);
        $invoiceLineItem->invoice_id = $invoice->id;
        $item = Item::findOne(['code' => Item::OPENING_BALANCE_ITEM]);
        $invoiceLineItem->item_id = $item->id;
        $invoiceLineItem->item_type_id = ItemType::TYPE_OPENING_BALANCE;
        $invoiceLineItem->description = $item->description;
        $invoiceLineItem->unit = 1;
        if ($this->isCredit) {
            $invoiceLineItem->unit = -1;
        }
        $invoiceLineItem->amount = $this->amount;
        $invoiceLineItem->code = $invoiceLineItem->getItemCode();
        $invoiceLineItem->cost = 0;
        $invoiceLineItem->save();
        $invoice->tax = $invoiceLineItem->tax_rate;
        $invoice->total = $invoice->subTotal + $invoice->tax;
        if (!empty($invoice->location->conversionDate)) {
            $date = Carbon::parse($invoice->location->conversionDate);
            $invoice->date = $date->subDay(1);
        }
        $invoice->save();
        return $invoice;
    }
}
