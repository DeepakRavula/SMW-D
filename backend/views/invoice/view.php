<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title. '#' .$model->id;
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
</style>
<div class="invoice-view p-10">
    <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <?php echo Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right', 'target'=>'_blank',]) ?>  
          <div class="pull-left invoice-address">
          <small><?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->address)): ?>
                <?php echo $model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->address?>
			<?php endif;?>
			<?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->phone_number)): ?><br>
            <?php echo $model->lineItems[0]->lesson->enrolment->student->customer->userLocation->location->phone_number?>
			<?php endif;?> 
      </small> 
      </div>
      <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info">
        <!-- /.col -->
        <div class="col-sm-4 invoice-col m-b-20">
        Bill To,
          <div class="row m-t-10">
            <div class="col-xs-4">
              <strong>Name:</strong>
            </div>
            <div class="col-xs-8">
              <strong><?php echo isset($model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity : null?></strong>
            </div>
          </div>
            <div class="row">
              <div class="col-xs-4">
				<?php if( ! empty($model->lineItems[0]->lesson->enrolment->student->customer->email)): ?>
                <strong>Email:</strong> 
              </div>
              <div class="col-xs-8">
                <?php echo $model->lineItems[0]->lesson->enrolment->student->customer->email?>
			<?php endif;?>
              </div>
            </div>
            <?php
                $addresses = $model->lineItems[0]->lesson->enrolment->student->customer->addresses;
                foreach($addresses as $address){
                  if($address->label === 'Billing'){
                    $billingAddress = $address;
                    break;
                  }
                }
                $phoneNumber = $model->lineItems[0]->lesson->enrolment->student->customer->phoneNumber; 
            ?>
<!-- Billing address -->
            <?php if(! empty($billingAddress)){ ?>
            <div class="row">
              <div class="col-xs-4">
                <strong><?php echo 'Billing Address:'; ?></strong>
              </div>
              <div class="col-xs-8">
                <?php 
                    echo $billingAddress->address . '<br> ' . $billingAddress->city->name . ', ';
                    echo $billingAddress->province->name . '<br>' . $billingAddress->country->name . ', ';
                    echo $billingAddress->postal_code;
                ?>
              </div>
            </div>
            <?php } ?>
<!-- Phone number -->
          <?php if(! empty($phoneNumber)){ ?>
            <div class="row">
              <div class="col-xs-4">
                <strong><?php echo 'Phone Number:'; ?></strong>
              </div>
              <div class="col-xs-8">
                <?php echo $phoneNumber->number;?>
              </div>
            </div>
           <?php } ?>
        </div>
        <!-- /.col -->
        <br>
        <div class="col-sm-4 invoice-col m-t-10">
          <div class="row">
            <div class="col-xs-4">
              <strong>Invoice Number: </strong>
            </div>
            <div class="col-xs-7">
              #<?php echo $model->invoice_number;?>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-4">
              <strong>Date: </strong>
            </div>
            <div class="col-xs-7">
              <?php echo date("d/m/Y", strtotime($model->date));?>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-4">
              <strong>Status: </strong>
            </div>
            <div class="col-xs-7">
              <?php echo $model->status($model);?>
            </div>
          </div>
        </div>
        <!-- /.col -->
      </div>
    <div>
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
            				'label' => 'Weight',
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
        <div class="col-xs-12">
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <?php if(! empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong>Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif;?>
                      <?php if(! empty($model->notes) && ! empty($model->internal_notes)):?>
                      <hr class="right-side-faded">
                      <?php endif;?>
                      <?php if(! empty($model->internal_notes)):?>
                      <div class="row-fluid">
                      <em><strong>Internal notes: <?php echo $model->internal_notes; ?></strong></em>
                    </div>
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
					           <tr>
                      <td>Paid</td>
                      <td><?php echo '0.00';?></td> 
                    </tr>
					           <tr>
                      <tr>
                      <td><strong>Total</strong></td>
                      <td><strong><?php echo $model->total;?></strong></td> 
                    </tr>
                      <td class="p-t-20">Balance</td>
                      <td class="p-t-20"><?php echo $model->total;?></td> 
                    </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.col -->
        </div>
<div class="clearfix"></div>
<div class="row no-print">
  <div class="col-xs-12">
  </div>
</div>
</div>
