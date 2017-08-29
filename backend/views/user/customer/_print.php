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
<div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <span class="logo-lg"><b>Arcadia</b>SMW</span>
          <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
          <?php $locationId = Yii::$app->session->get('location_id');
		  $location = Location::findOne(['id' => $locationId]);?>
		  
        From
        <address>
          <strong> <?php if (!empty($location->name)): ?>
                <?= $location->name?>
      <?php endif; ?></strong><br>
         <?php if (!empty($location->address)): ?>
                <?= $location->address?>
      <?php endif; ?><br>
         <?php if (!empty($location->city->name)): ?>
                <?= $location->city->name?>,
      <?php endif; ?><?php if (!empty($location->province->name)): ?>
                <?= $location->province->name?>
      <?php endif; ?><br>
      <?php if (!empty($location->postal_code)): ?>
                <?= $location->postal_code?>
      <?php endif; ?><br>
       <?php if (!empty($location->phone_number)): ?>
              Phone:<?= $location->phone_number?>
      <?php endif; ?><br>
          <?php if (!empty($location->email)): ?>
              E-mail:<?= $location->email?>
      <?php endif; ?>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        To
        <address>
          <strong><?php echo isset($model->publicIdentity) ? $model->publicIdentity : null?></strong><br>
          <?php
                $addresses = $model->addresses;
                foreach ($addresses as $address) {
                    if ($address->label === 'Billing') {
                        $billingAddress = $address;
                        break;
                    }
                }
                $phoneNumber = $model->phoneNumber;

                ?>
          <!-- Billing address -->
                <?php if (!empty($billingAddress)) {
                    ?>
                  <?php 
                        echo $billingAddress->address.'<br> '.$billingAddress->city->name.', ';
                    echo $billingAddress->province->name.'<br>';
                    echo $billingAddress->postal_code.'<br/>';
                } ?>
         <?php if (!empty($phoneNumber)): ?>
              Phone:<?= $phoneNumber->number?><br/>
      <?php endif; ?>
         <?php if (!empty($model->email)): ?>
              E-mail:<?= $model->email?>
      <?php endif; ?>
        </address>
      </div>
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
         'summary' =>'',
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
            'attribute' => 'total',
            'label' => 'Total',
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