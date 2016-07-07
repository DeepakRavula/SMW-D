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
<?php //echo '<pre>'; print_r($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer); ?>

<div class="invoice-view p-10">
    <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <?php echo Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right', 'target'=>'_blank',]) ?>  
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
              <strong><?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null?></strong>
            </div>
          </div>
            <div class="row">
              <div class="col-xs-4">
                <strong>Email:</strong> 
              </div>
              <div class="col-xs-8">
                <?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email : null?>
              </div>
            </div>
            <?php
                $addresses = $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->addresses;
                foreach($addresses as $address){
                  if($address->label === 'Billing'){
                    $billingAddress = $address;
                    break;
                  }
                }
                $phoneNumber = $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->phoneNumber; 
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
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->student->fullName) ? $data->lesson->enrolmentScheduleDay->enrolment->student->fullName : null;
            				},
            			    ],
						            				[
            				'label' => 'Program Name',
            				'value' => function($data) {
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name : null;
            				},
            			    ],
	
                [ 
                'attribute' => 'unit',
                'label' => 'Unit',
                'enableSorting' => false,
                ],
								[
            				'label' => 'Weight',
            				'value' => function($data) {
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->rate) ? $data->lesson->enrolmentScheduleDay->enrolment->qualification->program->rate : null;
            				},
            		],
                [ 
                'attribute' => 'amount',
                //'format' => 'currency',
                'label' => 'Amount',
                'enableSorting' => false,
                ],
                [
                'attribute' => 'amount',
                //'format' => 'currency',
                'label' => 'Total',
                'enableSorting' => false,
                ]
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>
    <?php //echo $this->render('_invoice_table-details' ); ?>
    <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <!-- <p class="lead">Balance : <?php //echo $model->total;?> </p> -->
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <div class="row-fluid m-t-10">
					<?php if(! empty($model->notes)):?>
                    <em><strong>Printed Notes: </strong><?php echo $model->notes; ?></em>
					<?php endif;?>
                    </div>
                    <hr class="right-side-faded">
                    <div class="row-fluid">
					<?php if(! empty($model->internal_notes)):?>
                    <em><strong>Internal notes: <?php echo $model->internal_notes; ?></strong></em>
					<?php endif;?>
                    </div>
                  </td>
                  <td colspan="2">
                    <table>
                    <!-- <tr>
                      <td style="width: 100px;"><strong>Tax:</strong></td>
                      <td style="width: 186px;"><?php echo Yii::$app->formatter->asCurrency($model->tax);?></td>
                    </tr> -->
                    <tr>
                      <td style="width: 100px;"><strong>Total</strong></td>
                      <td style="width: 135px;"><?php echo $model->total;//echo Yii::$app->formatter->asCurrency($model->total);?></td> 
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
    <?php //echo Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default', 'target'=>'_blank',]) ?>
  </div>
</div>
</div>
