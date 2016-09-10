<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;
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
                  <h3 class="box-title">Private Enrolments</h3>
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
            <div class="box box-danger box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Group Enrolments</h3>
                  <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <span class="info-box-number text-center"><?= $groupEnrolments ?></span>
                </div>
            <!-- /.box-body -->
            </div>
          <!-- /.box -->
        </div>
        <div class="col-md-3">
            <div class="box box-warning box-solid">
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
<?= Highcharts::widget([
   'options' => [
      'title' => ['text' => 'Monthly Revenue'],
      'xAxis' => [
         'categories' => Dashboard::previousMonths()
      ],
      'yAxis' => [
         'title' => ['text' => 'Income'],
      ],
      'series' => [
         ['name' => 'Month', 'data' =>  Dashboard::income()]
      ]
   ]
]);?>
</div>
<div class="col-md-6">
<?=  Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Instruction Hours'],
        'plotOptions' => [
            'pie' => [
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                ]
            ],
        ],
        'series' => [
            [ // new opening bracket
                'type' => 'pie',
                'name' => 'Elements',
                'data' => $completedPrograms,
            ] // new closing bracket
        ],
    ],
]);?>
</div>