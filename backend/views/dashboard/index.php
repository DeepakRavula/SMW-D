<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;

$this->title = 'Dashboard';
?>
<?php $this->registerCssFile("@web/css/dashboard/style.css");?>
<div class="dashboard-search pull-right form-inline">
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<?php yii\widgets\Pjax::begin(['id' => 'dashboard']); ?>
<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-maroon">
            <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>

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
		<div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-user-plus"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Enrolment Gains</span>
				<span class="info-box-number"><?= !empty($enrolmentGainCount) ? $enrolmentGainCount : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-user-times"></i></span>

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
		<div class="info-box bg-blue">
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
		<div class="info-box bg-gray">
            <span class="info-box-icon"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
				<span class="info-box-text">Group Enrolments</span>
				<span class="info-box-number"><?= $groupEnrolments ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-orange">
            <span class="info-box-icon"><i class="fa fa-child"></i></span>

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
		<div class="info-box bg-teal">
            <span class="info-box-icon"><i class="fa fa-cart-plus"></i></span>
            <div class="info-box-content">
				<span class="info-box-text">Payment Received</span>
				<span class="info-box-number"><?= !empty($payments) ? $payments : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-fuchsia">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
				<span class="info-box-text">Tax Collected</span>
				<span class="info-box-number"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-purple">
            <span class="info-box-icon"><i class="fa fa-flag"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Royalty Free Items</span>
				<span class="info-box-number"><?= !empty($royaltyPayment) ? $royaltyPayment : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box bg-navy">
            <span class="info-box-icon"><i class="fa fa-usd"></i></span>

            <div class="info-box-content">
				<span class="info-box-text">Total</span>
				<span class="info-box-number"><?php $total = $payments - ($invoiceTaxTotal + $royaltyPayment); ?>  <?= !empty($total) ? $total : 0 ?></span>
            </div>
            <!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
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
