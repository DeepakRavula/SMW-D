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
    table>thead>tr>th:last-child,
    table>tbody>tr>td:last-child{
      text-align: right;
    }
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
      .text-gray{
        color: gray !important;
      }
      .invoice-labels{
        width: 82px;
      }
    }
</style>
<div class="invoice-view p-10">
    <div class="row">
        <div class="col-xs-12 p-0">
          <h2 class="m-0">
            <a class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <div class="pull-left invoice-address  text-gray">
            <div class="row-fluid">
              <h2 class="m-0 text-inverse"><strong><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'INVOICE'?> </strong></h2>
          </div>
          <small><?php if (!empty($model->user->userLocation->location->address)): ?>
                <?php echo $model->user->userLocation->location->address?>
      <?php endif; ?>
      <?php if (!empty($model->user->userLocation->location->phone_number)): ?><br>
            <?php echo $model->user->userLocation->location->phone_number?>
      <?php endif; ?> 
      </small> 
      </div>
      <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info m-t-20">
        <!-- /.col -->
        <div class="col-sm-9 invoice-col m-b-20 pull-left p-0">
          <div class="row m-t-10">
            <div class="col-xs-12">
                <h4 class="m-0 f-w-400"><strong><?php echo isset($model->user->publicIdentity) ? $model->user->publicIdentity : null?></strong></h4>
            <div class="text-gray">
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
                <div class="row-fluid m-t-20">
                  <?php if (!empty($model->user->email)): ?>
                  <?php echo 'E: '; ?><?php echo $model->user->email?>
                  <?php endif; ?>
                </div>
              </div>
            <!-- Phone number -->
            <div class="row-fluid text-gray">
              <?php if (!empty($phoneNumber)) {
                    ?><?php echo 'P: '; ?>
              <?php echo $phoneNumber->number;
                } ?>
            </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col m-t-10 text-right p-0">
            <div class="row-fluid  text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#'.$model->invoice_number?></div>
              <div class="col-md-4 pull-left"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= Yii::$app->formatter->asDate($model->date); ?></div>
              <div class="col-md-4 pull-left">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div class="row-fluid text-gray">
			  <?php if ((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
				  <div class="col-md-4 pull-right text-right p-r-0"><?= $model->getStatus(); ?></div>
				  <div class="col-md-4 pull-left">Status:</div>
			<?php endif; ?>
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="clearfix"></div>
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' => ['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
                [
                    'label' => 'Description',
                    'format' => 'raw',
                    'value' => function ($data) {
                        if (!empty($data->discount)) {
                            if ((int) $data->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                                $discount = Yii::$app->formatter->format($data->discount, ['currency']);
                                $discountDiscription = '(Discount - ' . $discount . ')' ;
                                $discription = $data->description . "<br><center>" .
                                    $discountDiscription . "</center>";
                            } else {
                                $discount = $data->discount . '%';
                                $discountDiscription = '(Discount - ' . $discount . ')' ;
                                $discription = $data->description . "<br><center>" .
                                    $discountDiscription . "</center>";
                            }
                        } else {
                            $discription = $data->description;
                        }
                        return $discription;
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
				[
                    'format' => 'currency',
                    'label' => 'Sell',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
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
                    'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
				],
				[
                    'format' => 'currency',
					'label' => 'Net Price',
                    'value' => function ($data) {
                        return $data->netPrice;
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
                ],
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>
    <div class="row">
        <!-- /.col -->
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                    <td colspan="4">
        <?php if (!empty($model->payments)) : ?>
        <?php yii\widgets\Pjax::begin(['id' => 'payment-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $paymentsDataProvider,
            'tableOptions' => ['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
                [
                    'label' => 'Payment Method',
                    'value' => function ($data) {
                        return $data->paymentMethod->name;
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'format' => 'currency',
					'label' => 'Amount',
					'value' => function ($data) {
						return $data->invoice->getInvoicePaymentMethodTotal($data->payment_method_id);
					},
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center', 'style' => 'width:80px;'],
				],
            ],
        ]); ?>
        <?php yii\widgets\Pjax::end(); ?>
        <?php endif; ?>
                  <td colspan="2">
                    <table class="table-invoice-childtable">
                     <tr>
                      <td>SubTotal</td>
                      <td><?= Yii::$app->formatter->format($model->netSubtotal, ['currency']); ?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
                      <td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
                    </tr>
					<tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
                    </tr>
                    <tr>
                      <td>Paid</td>
                     <td><?= Yii::$app->formatter->format($model->paymentTotal, ['currency']); ?></td>
                    </tr>
                    <tr>
                      <td class="p-t-20"><strong>Balance</strong></td>
                      <td class="p-t-20"><strong><?= Yii::$app->formatter->format($model->invoiceBalance, ['currency']); ?></strong></td>
                    </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <!-- /.col -->
        </div>
    <div>
        <?php echo $model->reminderNotes; ?>
    </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>