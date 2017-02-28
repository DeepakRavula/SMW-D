<?php

use yii\grid\GridView;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use common\models\ItemType;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    table>thead>tr>th:first-child,
    table>tbody>tr>td:first-child{
        text-align: left !important;
    }
    .table-invoice-childtable>tbody>tr>td:last-of-type {
        text-align: right;
    }
    /*table>thead>tr>th:last-child,
    table>tbody>tr>td:last-child{
      text-align: right;
    }*/
    .table-invoice-childtable>tbody>tr>td:first-of-type{
      width: 230px;
    }
    .invoice-view .logo>img{
      width:135px;
    }
    
    .badge{
      border-radius: 50px;
      font-size: 18px;
      font-weight: 400;
      padding: 7px 30px;
      background: #ea212c;
    }
    @media print{
      .invoice-info{
        margin-top: 5px;
      }
      .text-gray{
        color: gray !important;
      }
      .invoice-labels{
        width: 82px;
      }
      .text-left{
        text-align: left !important;
      }
      .reminder_notes{
        position: fixed;
        bottom:0;
      }
      .notes-table{
        width:90vw;
      }
      .notes-table, .payment-method-table{
        vertical-align: top;
      }
      .subtotal-table{
        float:right;
      }
      .below-description{
        width:100%;
      }
      .table-invoice-childtable{
        width: 10vw;
        float:right !important;

      }
      .table-invoice-childtable>thead>tr>th:last-child, 
      .table-invoice-childtable>tbody>tr>td:last-child{
        white-space: nowrap;
      }
      .table-invoice-childtable>tbody>tr>td:first-of-type{
        width: 7vw;
      }
      .last-balance{
        border-top:1px solid #eaeaea;
      }
      .border-bottom-gray{
        border-bottom:1px solid #efefef;
      }

    }
</style>
<div class="invoice-view p-10">
<div class="row">
            <a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo invoice-col col-sm-2">              
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <div class="col-sm-3 invoice-col text-gray" style="font-size:18px;">
              <div class="row-fluid">
                <h2 class="m-0 text-inverse"><strong>
                  <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong>
                </h2>
              </div>
              <small>
                <?php if (!empty($model->user->userLocation->location->address)): ?>
                  <?= $model->user->userLocation->location->address?><br>
                <?php endif; ?>
                <?php if (!empty($model->user->userLocation->location->phone_number)): ?>
                  <?= $model->user->userLocation->location->phone_number?>
                <?php endif; ?> 
              </small> 
            </div>
            <div class="col-sm-4 invoice-col">
              To<br>
              <strong>
               <?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?>
               </strong>
              <address>
                <?php
                $addresses = $model->user->addresses;
                foreach ($addresses as $address) {
                    if ($address->label === 'Billing') {
                        $billingAddress = $address;
                        break;
                    }
                }
                $phoneNumber = $model->user->phoneNumber;

                ?>
                <!-- Billing address -->
                <?php if (!empty($billingAddress)) {
                    ?>
                  <?php 
                        echo $billingAddress->address.'<br> '.$billingAddress->city->name.', ';
                    echo $billingAddress->province->name.'<br>'.$billingAddress->country->name.' ';
                    echo $billingAddress->postal_code;
                } ?>
                <div class="row-fluid m-t-5">
                  <?php if (!empty($model->user->email)): ?>
                  <?php echo 'E: '; ?><?php echo $model->user->email?>
                  <?php endif; ?>
                </div>
            <!-- Phone number -->
            <div class="row-fluid text-gray">
              <?php if (!empty($phoneNumber)) {
                    ?><?php echo 'P: '; ?>
              <?php echo $phoneNumber->number;
                } ?>
            </div>
              </address>
            </div>
            <div class="col-sm-2 invoice-col">
              <b><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Invoice No.:'?></b> <?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#'.$model->invoice_number?><br>
              <b>Date:</b> <?= Yii::$app->formatter->asDate($model->date); ?><br>
              <b>Status:</b> <?= $model->getStatus(); ?>
            </div>
          <div class="clearfix"></div>
        </div>
    <div class="row invoice-info m-t-10">
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' => ['class' => 'table table-bordered m-0 table-more-condensed'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
                [
                    'label' => 'Description',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if (!empty($data->discount)) {
                            if ((int) $data->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                                $discount = Yii::$app->formatter->format($data->discount, ['currency']);
                                $discountDiscription = ' (Discount - ' . $discount . ')' ;
                                $discription = $data->description . "<i>" .
                                    $discountDiscription . "</i>";
                            } else {
                                $discount = $data->discount . '%';
                                $discountDiscription = ' (Discount - ' . $discount . ')' ;
                                $discription = $data->description . "<i>" .
                                    $discountDiscription . "</i>";
                            }
                        } else {
                            $discription = $data->description;
                        }
                        return $discription;
                    },
                    'headerOptions' => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left'],
                ],
				[
                    'format' => 'currency',
                    'label' => 'Sell',
                    'headerOptions' => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width:80px;'],
                    'value' => function ($data) {
                        if ((int) $data->item_type_id === (int) ItemType::TYPE_PRIVATE_LESSON) {
                            return $data->lesson->enrolment->program->rate;
                        } else {
                            return $data->amount;
                        }
                    },
                ],
                [
					'label' => 'Qty',
					'value' => function ($data) {
                        return $data->unit;
                    },
                    'headerOptions' => ['class' => 'text-left'],
					'contentOptions' => ['class' => 'text-left', 'style' => 'width:50px;'],
				],
				[
                    'format' => 'currency',
					'label' => 'Net Price',
                    'value' => function ($data) {
                        return $data->netPrice;
                    },
                    'headerOptions' => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left', 'style' => 'width:80px;'],
                ],
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>
   

    <table class="table-responsive below-description ">
        <tr>
            <td class="notes-table">
                <?php if (!empty($model->notes)):?>
                <div class="row-fluid m-t-15 m-b-15">
                    <em><strong> Notes: </strong><Br>
                    <?php echo $model->notes; ?></em>
                </div>
                <?php endif; ?>
            </td>
            <td rowspan="2" class="subtotal-table p-t-10">
                <table class="table-invoice-childtable table-more-condensed">
                    <tr>
                        <td>SubTotal</td>
                        <td>
                            <?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td>
                            <?= Yii::$app->formatter->format($model->tax, ['currency']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td>
                            <?= Yii::$app->formatter->format($model->invoicePaymentTotal, ['currency']); ?>
                        </td>
                    </tr>
                    <tr class="last-balance">
                        <td class="p-t-0"><strong>Balance</strong></td>
                        <td class="p-t-0"><strong><?= Yii::$app->formatter->format($model->balance, ['currency']); ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="payment-method-table">
              <?php if (!empty($model->payments)) : ?>
                    <?php yii\widgets\Pjax::begin(['id' => 'payment-index']); ?>
                    <?php echo GridView::widget([
                          'dataProvider' => $paymentsDataProvider,
                          'tableOptions' => ['class' => 'table  m-0 table-more-condensed inner-payment-table'],
                          'headerRowOptions' => ['class' => 'bg-light-gray'],
                          'columns' => [
                              [
                                  'label' => 'Payment',
                                  'value' => function ($data) {
                                      return $data->paymentMethod->name;
                                  },
                                  'headerOptions' => ['class' => 'text-left'],
                                  'contentOptions' => ['class' => 'text-left'],
                              ],
							[
                                  'value' => function ($data) {
                                      return !empty($data->reference) ? $data->reference : null;
                                  },
                              ],
                              [
                                  'format' => 'currency',
                                  'value' => function ($data) {
                                    return $data->invoice->getInvoicePaymentMethodTotal($data->payment_method_id);
                                  },
                                  'headerOptions' => ['class' => 'text-left'],
                                  'contentOptions' => ['class' => 'text-left', 'style' => 'width:80px;'],
                              ],
                          ],
                    ]); ?>
                    <?php yii\widgets\Pjax::end(); ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>
        <!-- /.col -->
        </div>
    <div class="reminder_notes text-muted well well-sm no-shadow">
        <?php echo $model->reminderNotes; ?>
    </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>