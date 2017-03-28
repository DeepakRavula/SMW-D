<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;
use common\models\Invoice;
use common\models\Payment;

$this->title = 'Dashboard';
?>
<div class="col-md-12">
    <h3 class="pull-left">Dashboard</h3>
    <div class="pull-right col-xs-12 col-md-6 p-10 p-r-0"><?php echo $this->render('_search', ['model' => $searchModel]); ?></div>
    <div class="clearfix"></div>
    <div class="col-md-10 p-0">
    <div class="row text-center bg-gray disabled color-palette">
        <div class="col-md-3 p-0 ">
          <div class="small-box">
            <div class="inner">
              <h3><?= !empty($invoiceTotal) ? $invoiceTotal : 0 ?></h3>
              <p>Invoice Total</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 p-0">
          <div class="small-box">
            <div class="inner">
              <h3><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></h3>
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
              <h3><?= $students ?></h3>
              <p>Active Students</p>
            </div>
          </div>
        </div>
    </div>

<div>
<?= Highcharts::widget([
   'options' => [
      'title' => ['text' => 'Monthly Revenue'],
      'xAxis' => [
         'categories' => Dashboard::previousMonths(),
      ],
      'yAxis' => [
         'title' => ['text' => 'Income'],
      ],
      'series' => [
         [
         'name' => 'Month', 
         'data' => Dashboard::income(),
         'color'=> '#E12E2B'
         ],
      ],
   ],
]); ?>
</div>
<div class="m-t-20     instruction_hours_piechart">
<?=  Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Instruction Hours'],
        'plotOptions' => [
            'pie' => [
                'size' => '80%',
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                ],
            ],
        ],
        'series' => [
            [ // new opening bracket
                'type' => 'pie',
                'name' => 'Hours',
                'data' => $completedPrograms
            ], // new closing bracket
        ],
    ],
]); ?>
</div>
</div>
<div class="col-md-2 col-sm-4 p-r-0">
                  <div class="pad box-pane-right bg-green" style="min-height: 280px">
                    <div class="description-block margin-bottom">
                      <h5 class="description-header"><?= !empty($payments) ? $payments : 0 ?></h5>
                      <span class="description-text">Payment Received</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block margin-bottom">
                      <h5 class="description-header"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></h5>
                      <span class="description-text">Tax Collected</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block">
                      <h5 class="description-header"><?= !empty($royaltyPayment) ? $royaltyPayment : 0 ?></h5>
                      <span class="description-text">Royalty Free Items</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block">
                      <h5 class="description-header"><?php $total = $payments - ($invoiceTaxTotal + $royaltyPayment); ?>  <?= !empty($total) ? $total : 0 ?></h5>
                      <span class="description-text">Total</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                </div>