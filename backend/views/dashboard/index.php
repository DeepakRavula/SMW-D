<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Invoice;

?>
<div class="col-md-12">
    <h3>Dashboard</h3>

    <div class="row">
        <div class="col-md-3">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Total</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $invoiceTotal ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Tax Total</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $invoiceTaxTotal ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Enrolled Students</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $enrolments ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Payments</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $payments ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Active Students</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $students ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
    </div>
</div>
<div class="col-md-6">
<?php
	$start = new DateTime('first day of this month - 5 months');
	$end = new DateTime();
	$interval = new DateInterval('P1M');

	$date_period = new DatePeriod($start, $interval, $end);
	$months = [];
	foreach ($date_period as $dates) {
		array_push($months, $dates->format('M'));
	}

	
;?>
	<?= Highcharts::widget([
   'options' => [
      'title' => ['text' => 'Monthly Revenue'],
      'xAxis' => [
         'categories' => $months
      ],
      'yAxis' => [
         'title' => ['text' => 'Income'],
      ],
      'series' => [
         ['name' => 'month', 'data' => [10500, 25800, 42275, 22389.5, 35000, 55555]]
      ]
   ]
]);?>
</div>