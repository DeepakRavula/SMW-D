<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>
<?php $this->registerCssFile("@web/css/dashboard/style.css"); ?>
<div class="dashboard-search pull-right form-inline">
	<?php $total = $payments - ($invoiceTaxTotal + $royaltyPayment); ?>
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<?php yii\widgets\Pjax::begin(['id' => 'dashboard']); ?>

<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'maroon',
			'number' => !empty($invoiceTotal) ? $invoiceTotal : 0,
			'text' => 'Invoice Total',
			'icon' => 'fa fa-file-text-o',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'aqua',
			'number' => !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0,
			'text' => 'Invoice Tax Total',
			'icon' => 'fa fa-bookmark-o',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'green',
			'number' => !empty($enrolmentGainCount) ? $enrolmentGainCount : 0,
			'text' => 'Enrolment Gains',
			'icon' => 'fa fa-user-plus',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'red',
			'number' => !empty($enrolmentLossCount) ? $enrolmentLossCount : 0,
			'text' => 'Enrolment Losses',
			'icon' => 'fa fa-user-times',
		])
		?>
	</div>	
</div>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'blue',
			'number' => $enrolments,
			'text' => 'Private Enrolments',
			'icon' => 'fa fa-graduation-cap',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'gray',
			'number' => $groupEnrolments,
			'text' => 'Group Enrolments',
			'icon' => 'fa fa-users',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'orange',
			'number' => $students,
			'text' => 'Active Students',
			'icon' => 'fa fa-child',
		])
		?>
	</div>
</div>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
		echo \insolita\wgadminlte\LteInfoBox::widget([
			'bgIconColor' => '',
			'bgColor' => 'teal',
			'number' => !empty($payments) ? $payments : 0,
			'text' => 'Payment Received',
			'icon' => 'fa fa-cart-plus',
		])
		?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
<?php
echo \insolita\wgadminlte\LteInfoBox::widget([
	'bgIconColor' => '',
	'bgColor' => 'fuchsia',
	'number' => !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0,
	'text' => 'Tax Collected',
	'icon' => 'fa fa-money',
])
?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
<?php
echo \insolita\wgadminlte\LteInfoBox::widget([
	'bgIconColor' => '',
	'bgColor' => 'purple',
	'number' => !empty($royaltyPayment) ? $royaltyPayment : 0,
	'text' => 'Royalty Free Items',
	'icon' => 'fa fa-flag',
])
?>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
<?php
echo \insolita\wgadminlte\LteInfoBox::widget([
	'bgIconColor' => '',
	'bgColor' => 'navy',
	'number' => !empty($total) ? $total : 0,
	'text' => 'Total',
	'icon' => 'fa fa-usd',
])
?>
	</div>
</div>
<div class="row">
	<div class="col-md-7">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Monthly Revenue</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
				<div class="row">
					<div class="col-md-12 col-sm-8">
						<div class="pad">
							<!-- Map will be created here -->
<?=
Highcharts::widget([
	'options' => [
		'title' => ['text' => ''],
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
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
	<div class="col-md-5">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Instruction Hours</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
				<div class="row">
					<div class="col-md-12 col-sm-8">
						<div class="pad">
							<!-- Map will be created here -->
							<?php if (!empty($completedPrograms)) : ?>
								<?=
								Highcharts::widget([
									'options' => [
										'title' => ['text' => ''],
										'plotOptions' => [
											'pie' => [
												'showInLegend' => true,
												'size' => '80%',
												'cursor' => 'pointer',
												'dataLabels' => [
													'enabled' => false,
													'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
												],
											],
										],
										'series' => [
												[
												'type' => 'pie',
												'name' => 'Hours',
												'data' => $completedPrograms
											],
										],
									],
								]);
								?>
<?php endif; ?>	
						</div>
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
	<!-- /.col -->
</div>
<div class="row">
	<div class="col-md-7">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Enrolment Gains</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
				<div class="row">
					<div class="col-md-12 col-sm-8">
						<div class="pad">
							<!-- Map will be created here -->
							<?php if ($enrolmentGains) : ?>
								<?=
								Highcharts::widget([
									'options' => [
										'title' => ['text' => ''],
										'plotOptions' => [
											'pie' => [
												'showInLegend' => true,
												'size' => '80%',
												'cursor' => 'pointer',
												'dataLabels' => [
													'enabled' => false,
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
							<?php endif; ?>
						</div>
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
	<div class="col-md-5">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Enrolment Losses</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
				<div class="row">
					<div class="col-md-12 col-sm-8">
						<div class="pad">
							<!-- Map will be created here -->
								<?php if (!empty($enrolmentLosses)) : ?>
								<div class="m-t-20">
									<?=
									Highcharts::widget([
										'options' => [
											'title' => ['text' => ''],
											'plotOptions' => [
												'pie' => [
													'showInLegend' => true,
													'size' => '80%',
													'cursor' => 'pointer',
													'dataLabels' => [
														'enabled' => false,
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
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
	<!-- /.col -->
</div>
<?php yii\widgets\Pjax::end(); ?>
