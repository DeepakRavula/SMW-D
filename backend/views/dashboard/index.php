<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use common\models\Dashboard;

$this->title = 'Dashboard';

$this->params['action-button'] = $this->render('_search', ['model' => $searchModel]); ?>
<?php yii\widgets\Pjax::begin(['id' => 'dashboard']); ?>

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
	<div class="col-md-3 col-sm-6 col-xs-12">
		<?php
        echo \insolita\wgadminlte\LteInfoBox::widget([
            'bgIconColor' => '',
            'bgColor' => 'green',
            'number' => $lessonsCount,
            'text' => 'Lessons',
            'icon' => 'fa fa-music',
        ])
        ?>
	</div>
</div>

<?php if (Yii::$app->user->can('manageMonthlyRevenue')) : ?>
<div class="row">
	<div class="col-md-12">
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
</div>
<?php endif; ?> 
<div class="row">
<?php if (Yii::$app->user->can('manageEnrolmentGains')) : ?>
	<div class="col-md-4">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Enrolment Gains (<?= $enrolmentGainCount; ?>)</h3>
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
						</div>
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
    <?php endif; ?> 
    <?php if (Yii::$app->user->can('manageEnrolmentLosses')) : ?>
	<div class="col-md-4">
		<div class="box box-success">
            <div class="box-header with-border">
				<h3 class="box-title">Enrolment Losses (<?= $enrolmentLossCount; ?>)</h3>
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
						</div>
					</div>
				</div>
            </div>
            <!-- /.box-body -->
		</div>
	</div>
    <?php endif; ?> 
    <?php if (Yii::$app->user->can('manageInstructionHours')) : ?>
	<div class="col-md-4">
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
    <?php endif; ?> 
	<!-- /.col -->
</div>
<?php yii\widgets\Pjax::end(); ?>
