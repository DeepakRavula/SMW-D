<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\search\InvoiceSearch;

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
          <small><?php if( ! empty($model->user->userLocation->location->address)): ?>
                <?php echo $model->user->userLocation->location->address?>
      <?php endif;?>
      <?php if( ! empty($model->user->userLocation->location->phone_number)): ?><br>
            <?php echo $model->user->userLocation->location->phone_number?>
      <?php endif;?> 
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
                foreach($addresses as $address){
                  if($address->label === 'Billing'){
                    $billingAddress = $address;
                    break;
                  }
                }
                $phoneNumber = $model->user->phoneNumber; 
            
                ?>
                <!-- Billing address -->
                <?php if(! empty($billingAddress)){ ?>
                  <?php 
                        echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
                        echo $billingAddress->province->name . '<br>' . $billingAddress->country->name . ' ';
                        echo $billingAddress->postal_code;
                   } ?>
                <div class="row-fluid m-t-20">
                  <?php if( ! empty($model->user->email)): ?>
                  <?php echo 'E: '; ?><?php echo $model->user->email?>
                  <?php endif;?>
                </div>
              </div>
            <!-- Phone number -->
            <div class="row-fluid text-gray">
              <?php if(! empty($phoneNumber)){ ?><?php echo 'P: '; ?>
              <?php echo $phoneNumber->number; } ?>
            </div>
            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-sm-3 invoice-col m-t-10 text-right p-0">
            <div class="row-fluid  text-gray">
              <div class="col-md-4 pull-right text-right p-r-0"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : '#' . $model->invoice_number?></div>
              <div class="col-md-2 pull-right"><?= (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? '' : 'Number:'?> </div> 
              <div class="clearfix"></div>
            </div>
          <div class="row-fluid text-gray">
              <div class="col-xs-4 pull-right text-right p-r-0"><?= Yii::$app->formatter->asDate($model->date);?></div>
              <div class="invoice-labels col-xs-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          <div class="row-fluid text-gray">
			  <?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
				  <div class="col-md-4 pull-right text-right p-r-0">
				  <?= $model->getStatus();?></div>
				  <div class="col-md-2 pull-right">Status:</div>
			<?php endif;?>
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="clearfix"></div>
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' =>['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
                  [
                    'label' => 'Student Name',
                    'value' => function($data) {
                      return !empty($data->lesson->enrolment->student->fullName) ? $data->lesson->enrolment->student->fullName : null;
                    },
                      ],
                                [
                    'label' => 'Program Name',
                    'value' => function($data) {
                      return !empty($data->lesson->enrolment->program->name) ? $data->lesson->enrolment->program->name : null;
                    },
                      ],
  
                [ 
                'attribute' => 'unit',
                'label' => 'Unit',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'enableSorting' => false,
                ],
                [
                    'label' => 'Rate/hr',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function($data) {
                      return !empty($data->lesson->enrolment->program->rate) ? $data->lesson->enrolment->program->rate : null;
                    },
                ],
                [ 
                'attribute' => 'amount',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Amount',
                'enableSorting' => false,
                ],
                [
                'attribute' => 'amount',
                'label' => 'Total',
                'enableSorting' => false,
                ]
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
                    <?php if(! empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Printed Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif;?>
                      <?php if(! empty($model->notes) && ! empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
                      <?php endif;?>
                  </td>
                  <td colspan="2">
                    <table class="table-invoice-childtable">
                     <tr>
                      <td>SubTotal</td>
                      <td><?php echo $model->subTotal;?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
                      <td><?php echo $model->tax;?></td>
                    </tr>
                     <?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
					<tr>
                      <td>Paid</td>
                      <td><?= $model->invoicePaymentTotal;?></td> 
                    </tr>
				<?php endif;?>
                     <tr>
                      <tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?php echo $model->total;?></strong></td> 
                    </tr>
                     <?php if((int) $model->type === InvoiceSearch::TYPE_INVOICE):?>
                    </tr>
                      <td class="p-t-20">Balance</td>
                      <td class="p-t-20"><?= $model->invoiceBalance;?></td> 
                    </tr>
				<?php endif;?>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <!-- /.col -->
        </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>