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
        <div class="col-xs-12 p-0">
          <h2 class="m-0 border-bottom-gray">
            <a class="logo pull-left">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
          <div class="pull-left invoice-address  text-gray">
	      <div class="row-fluid">
              <h4 class="m-0 text-inverse"><strong>STATEMENT</strong></h4>
          </div>
          <small><?php
		  $locationId = Yii::$app->session->get('location_id');
		  $location = Location::findOne(['id' => $locationId]);
		  if (!empty($location->address)): ?>
                <?= $location->address?>
      <?php endif; ?>
      <?php if (!empty($location->phone_number)): ?><br>
            <?= $location->phone_number; ?>
      <?php endif; ?> 
      <?php if (!empty($location->email)): ?><br>
            <?= $location->email; ?>
      <?php endif; ?> 
      </small> 
      </div>
      <div class="clearfix"></div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
    <div class="row invoice-info m-t-10">
        <!-- /.col -->
        <div class="col-sm-9 invoice-col m-b-10 pull-left p-0">
          <div class="row m-T-10">
            <div class="col-xs-12">
                <h4 class="m-0 f-w-400"><strong><?php echo isset($model->publicIdentity) ? $model->publicIdentity : null?></strong></h4>
            <div class="text-gray">
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
                    echo $billingAddress->province->name.'<br>'.$billingAddress->country->name.' ';
                    echo $billingAddress->postal_code;
                } ?>
                <div class="row-fluid m-t-5">
                  <?php if (!empty($model->email)): ?>
                  <?php echo 'E: '; ?><?php echo $model->email?>
                  <?php endif; ?>
                </div>
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
		 <div class="col-sm-4 invoice-col m-t-10 text-right p-0">
          <div class="row-fluid text-gray">
              <div class="col-md-4 pull-right text-left p-r-0 p-l-30"><?= $dateRange; ?></div>
              <div class="col-md-2 pull-right">Date:</div>
              <div class="clearfix"></div>
          </div>
          </div> 
        </div>
        <!-- /.col -->
     
          <div class="clearfix"></div>
		  <?php echo  GridView::widget([
    'dataProvider' => $invoiceDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered m-0 table-more-condensed'],
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
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>