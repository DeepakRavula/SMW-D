<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;

$this->title = 'Dashboard';
?>
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="clearfix"></div>
<?php yii\widgets\Pjax::begin(['id' => 'dashboard']); ?>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Invoice Total</span>
              <span class="info-box-number"><?= !empty($invoiceTotal) ? $invoiceTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Invoice Tax Total</span>
              <span class="info-box-number"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Enrolment Gains</span>
              <span class="info-box-number"><?= !empty($enrolmentGainCount) ? $enrolmentGainCount : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	<div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Enrolment Losses</span>
              <span class="info-box-number"><?= !empty($enrolmentLossCount) ? $enrolmentLossCount : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
	</div>
<div class="col-md-12">
    <div class="col-md-10 p-0">
		<div class="row text-center bg-gray disabled color-palette">
			<div class="col-md-2 p-0">
				<div class="small-box">
					<div class="inner">
						<h3><?= $enrolments ?></h3>
						<p>Private Enrolments</p>
					</div>
				</div>
			</div>
			<div class="col-md-1 p-0">
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
</div>
<?php yii\widgets\Pjax::end(); ?>
