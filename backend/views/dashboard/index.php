<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;

$this->title = 'Dashboard';
?>
<style>
	.info-box-content {
		margin-left: 60px;
	}
	.info-box-icon {
		width: 60px;
	}
	</style>
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="clearfix"></div>
<?php yii\widgets\Pjax::begin(['id' => 'dashboard']); ?>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Invoice Total</span>
              <span class="info-box-number"><?= !empty($invoiceTotal) ? $invoiceTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Invoice Tax Total</span>
              <span class="info-box-number"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
		<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Enrolment Gains</span>
              <span class="info-box-number"><?= !empty($enrolmentGainCount) ? $enrolmentGainCount : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Enrolment Losses</span>
              <span class="info-box-number"><?= !empty($enrolmentLossCount) ? $enrolmentLossCount : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>	
	</div>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-graduation-cap"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Private Enrolments</span>
              <span class="info-box-number"><?= $enrolments ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-graduation-cap"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Group Enrolments</span>
              <span class="info-box-number"><?= $groupEnrolments ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-lg fa-fw fa-child"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Active Students</span>
              <span class="info-box-number"><?= $students ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </div>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-ios-cart-outline"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Payment Received</span>
              <span class="info-box-number"><?= !empty($payments) ? $payments : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-graduation-cap"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Tax Collected</span>
              <span class="info-box-number"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-lg fa-fw fa-child"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Royalty Free Items</span>
              <span class="info-box-number"><?= !empty($royaltyPayment) ? $royaltyPayment : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        </div>
<div class="col-md-12">
    <div class="col-md-10 p-0">
		<div>
			<?=
			Highcharts::widget([
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
							'color' => '#E12E2B'
						],
					],
				],
			]);
			?>
		</div>
		<?php if(!empty($completedPrograms)) : ?>
		<div class="m-t-20 instruction_hours_piechart">
			<?=
			Highcharts::widget([
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
							[// new opening bracket
							'type' => 'pie',
							'name' => 'Hours',
							'data' => $completedPrograms
						], // new closing bracket
					],
				],
			]);
			?>
		</div>
		<?php endif; ?>
		<?php if($enrolmentGains) : ?>
		<div class="m-t-20">
			<?=
			Highcharts::widget([
				'options' => [
					'title' => ['text' => 'Enrolment Gains'],
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
						[
							'type' => 'pie',
							'name' => 'Gain Count',
							'data' => $enrolmentGains
						], 
					],
				],
			]);
			?>
		</div>
		<?php endif;?>
		<?php if(!empty($enrolmentLosses)) : ?>
		<div class="m-t-20">
			<?=
			Highcharts::widget([
				'options' => [
					'title' => ['text' => 'Enrolment Losses'],
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
						[
							'type' => 'pie',
							'name' => 'Loss Count',
							'data' => $enrolmentLosses
						], 
					],
				],
			]);
			?>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-md-2 col-sm-4 p-r-0">
		<div class="pad box-pane-right bg-green" style="min-height: 280px">
			<div class="description-block">
				<h5 class="description-header"><?php $total = $payments - ($invoiceTaxTotal + $royaltyPayment); ?>  <?= !empty($total) ? $total : 0 ?></h5>
				<span class="description-text">Total</span>
			</div>
			<!-- /.description-block -->
		</div>
	</div>
</div>
<?php yii\widgets\Pjax::end(); ?>
