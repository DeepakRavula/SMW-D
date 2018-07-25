<?php

use yii\grid\GridView;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-view p-10">
<?php
   echo $this->render('/print/_header', [
       'userModel'=>$model,
       'locationModel'=>$model->userLocation->location,
]);
   ?>
    <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <b>Invoices</b><br>
        <br>
       
        <b><?= $dateRange; ?></b> 
      </div>
      <!-- /.col -->
    </div>
     <div class="clearfix"></div>
    <?php
     echo GridView::widget([
         'dataProvider' => $invoiceDataProvider,
         'options' => ['class' => 'col-md-12'],
         'summary' => false,
        'emptyText' => false,
         'tableOptions' => ['class' => 'table table-striped table-more-condensed'],
         'headerRowOptions' => ['class' => 'bg-light-gray'],
         'columns' => [
        [
            'label' => 'Invoice Number',
            'value' => function ($data) {
                return $data->getInvoiceNumber();
            },
        ],
        [
            'label' => 'Student Name',
            'value' => function ($data) {
                return !empty($data->lineItems[0]->lesson->enrolment->student->fullName) ? $data->lineItems[0]->lesson->enrolment->student->fullName.' ('.$data->lineItems[0]->lesson->enrolment->program->name.')' : null;
            },
        ],
        [
        'label' => 'Date',
            'value' => function ($data) {
                return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            },
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->getStatus();
            },
        ],
        [
            'value' => function ($data) {
                return round($data->total, 2);
            },
            'label' => 'Total',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'enableSorting' => false,
        ],
    ],
]); ?> 
              
              
              
      </div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>