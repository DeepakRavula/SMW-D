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
            <small class="pull-right">Date: <?php echo date("d/m/Y", strtotime($model->date));?></small>
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
            'tableOptions' =>['class' => 'table table-bordered m-0'],
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
        <!-- /.col -->
        <div class="col-xs-12">
          <!-- <p class="lead">Balance : <?php //echo $model->total;?> </p> -->
          <div class="table-responsive">
            <table class="table table-invoice-total">
              <tbody>
                <tr>
                  <td colspan="4">
                    <div class="row-fluid">
                    <em><strong>Notes: </strong><?php echo $model->notes; ?></em>
                    </div>
                    <hr>
                    <div class="row-fluid">
                    <em><strong>Internal notes: <?php echo $model->internal_notes; ?></strong></em>
                    </div>
                  </td>
                  <td colspan="2">
                    <table>
                    <tr>
                      <td style="width: 100px;"><strong>Tax:</strong></td>
                      <td style="width: 186px;"><?php echo 'CA$' .$model->tax;?></td>
                    </tr>
                    <tr>
                      <td style="width: 100px;"><strong>Total:</strong></td>
                      <td style="width: 186px;"><?php echo 'CA$' .$model->total;?></td> 
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
<!-- <div class="col-xs-4 notes">
  <label for="notes">Notes:</label>
  <textarea rows=4, cols=60, readonly =true, name="notes" ></textarea>
</div>
<div class="clearfix"></div>
<div class="col-xs-4 notes">
  <label for="notes">Internal Notes:</label>
  <textarea rows=4, cols=60, readonly =true, name="notes" ></textarea>
</div> -->
<div class="clearfix"></div>
<div class="row no-print">
  <div class="col-xs-12">
    <?php echo Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default', 'target'=>'_blank',]) ?>
  </div>
</div>
</div>
