<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Invoice;
use common\models\Payment;

?>
<div class="col-md-12">
    <h3>Dashboard</h3>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Total</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= ! empty($invoiceTotal) ? $invoiceTotal : 0 ?></span>
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
                <span class="info-box-number text-center"><?= ! empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
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
                <span class="info-box-number text-center"><?= ! empty($payments) ? $payments : 0 ?></span>
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
	$payment = [];
	foreach ($date_period as $dates) {
		array_push($months, $dates->format('M'));
	$fromDate = $dates->format('Y-m-d');
	$toDate = $dates->format('Y-m-t');
	 $locationId = Yii::$app->session->get('location_id');
$monthlyPayment = Payment::find()
                   ->joinWith(['invoice i' => function($query) use($locationId) {                        
                            $query->where(['i.location_id' => $locationId]);                        
                    }])
                    ->andWhere(['between','payment.date', $fromDate, $toDate])
                    ->sum('payment.amount');
					array_push($payment,$monthlyPayment);
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
         ['name' => 'Month', 'data' =>  $payment]
      ]
   ]
]);?>
</div>