<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;
use common\models\Invoice;
use common\models\Payment;

?>
<div class="col-md-12">
    <h3 class="pull-left">Dashboard</h3>
    <div class="pull-right col-xs-7 p-10"><?php echo $this->render('_search', ['model' => $searchModel]); ?></div>
    <div class="clearfix"></div>
    <div class="row text-center bg-gray disabled color-palette">
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= ! empty($invoiceTotal) ? $invoiceTotal : 0 ?></h3>
              <p>Invoice Total</p>
            </div>
          </div>
        </div>
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= ! empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></h3>
              <p>Invoice Tax Total</p>
            </div>
          </div>
        </div>
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= $enrolments ?></h3>
              <p>Private Enrolments</p>
            </div>
          </div>
        </div>
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= $groupEnrolments ?></h3>
              <p>Group Enrolments</p>
            </div>
          </div>
        </div>
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">

              <p>Payment Received : <?= ! empty($payments) ? $payments : 0 ?></p>
              <p>Tax Collected : - <?= ! empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></p>
              <p>Royalty Free Items : - 0</p>
			     <?php $total = $payments - $invoiceTaxTotal; ?>
              <p>Total : <?= ! empty($total) ? $total : 0 ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-2 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= $students ?></h3>
              <p>Active Students</p>
            </div>
          </div>
        </div>
    </div>
</div>

<div class="col-md-6 m-t-20">
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
<div class="col-md-6 m-t-20">
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