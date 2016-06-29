<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php //echo '<pre>'; print_r($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer); ?>

<div class="invoice-view">
    <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <div class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </div>
            <small class="pull-right">Date: <?php echo date("m/d/Y", strtotime($model->date));?></small>
            <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info">
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          To
          <address>
            <strong><?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->publicIdentity : null?></strong>
            <br>
            <strong>Email:</strong> <?php echo isset($model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email) ? $model->lineItems[0]->lesson->enrolmentScheduleDay->enrolment->student->customer->email : null?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Invoice:</b> #<?php echo $model->invoice_number;?> <br>
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
            'tableOptions' =>['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
            'columns' => [
      			    [
            				'label' => 'Teacher Name',
            				'value' => function($data) {
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->teacher->publicIdentity) ? $data->lesson->enrolmentScheduleDay->enrolment->qualification->teacher->publicIdentity : null;
            				},
            			    ],
            				[
            				'label' => 'Program Name',
            				'value' => function($data) {
            					return !empty($data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->lesson->enrolmentScheduleDay->enrolment->qualification->program->name : null;
            				},
            			    ],
            				[
            				'label' => 'Date',
            				'value' => function($data) {
            					$date = date("d-m-Y", strtotime($data->lesson->date)); 
            					return ! empty($date) ? $date : null;
                            },
            			    ],
            				[
            				'label' => 'From Time',
            				'value' => function($data) {
            					if(! empty($data->lesson->enrolmentScheduleDay->from_time)){
            						$fromTime = date("g:i a",strtotime($data->lesson->enrolmentScheduleDay->from_time));
            						return !empty($fromTime) ? $fromTime : null;
            					}
            					return null;
            				},
      			    ],
                [ 
                'attribute' => 'unit',
                'label' => 'Unit',
                'enableSorting' => false,
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
    <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
                <label for="notes">Notes:</label>
                <div class="clearfix"></div>
                <textarea rows=5, cols=110, readonly =true, name="notes" ><?php echo $model->notes; ?></textarea>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <p class="lead">Balance : <?php echo $model->total;?> </p>

          <div class="table-responsive">
            <table class="table">
              <tbody><tr style="border-top: 0">
                <th style="width:50%">Subtotal:</th>
                <td><?php echo $model->subTotal;?></td>
              </tr>
              <tr>
                <th>Tax</th>
                <td><?php echo $model->tax;?></td>
              </tr>
              <tr>
                <th>Paid:</th>
                <td>$0.00</td>
              </tr>
              <tr>
                <th>Total:</th>
                <td><?php echo $model->total;?></td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>
