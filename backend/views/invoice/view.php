<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = 'Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title. '#' .$model->id;
?>
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
        <div class="col-sm-4 invoice-col">
        Bill To,
          <address>
            <strong><?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null?></strong>
            <br>
            <strong>Email:</strong> <?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email : null?>
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
			 <br>
			 <?php
			if(! empty($billingAddress)){
				echo 'Billing Address:';
				echo $billingAddress->address . ', ' . $billingAddress->city->name;
				echo $billingAddress->province->name . ', ' . $billingAddress->country->name;
				echo $billingAddress->postal_code;
			}
			echo "<br>";
			if(! empty($phoneNumber)){
				echo 'Phone Number:';
				echo $phoneNumber->number;
			}
				?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
			<b>Date:</b> <?php echo date("d/m/Y", strtotime($model->date));?><br>
          <b>Invoice Number:</b> #<?php echo $model->invoice_number;?> <br>
          <b>Status:</b> <?php echo $model->status($model);?><br>
        </div>
        <!-- /.col -->
      </div>
    <div>
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
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->rate) ? Yii::$app->formatter->asCurrency($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->rate) : null;
            				},
            			    ],
                [ 
                'attribute' => 'amount',
                'format' => 'currency',
                'label' => 'Amount',
                'enableSorting' => false,
                ],
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
                    <em><strong>Printed Notes: </strong><?php echo $model->notes; ?></em>
                    </div>
                    <hr class="right-side-faded">
                    <div class="row-fluid">
                    <em><strong>Internal notes: <?php echo $model->internal_notes; ?></strong></em>
                    </div>
                  </td>
                  <td colspan="2">
                    <table>
                    <tr>
                      <td style="width: 100px;"><strong>Tax:</strong></td>
                      <td style="width: 186px;"><?php echo Yii::$app->formatter->asCurrency($model->tax);?></td>
                    </tr>
                    <tr>
                      <td style="width: 100px;"><strong>Total:</strong></td>
                      <td style="width: 186px;"><?php echo Yii::$app->formatter->asCurrency($model->total);?></td> 
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
