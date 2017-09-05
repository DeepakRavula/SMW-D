<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>
	
    <div class="row">
    <div class="col-md-5">
   <?php 
   echo DateRangePicker::widget([
    'model' => $model,
    'attribute' => 'dateRange',
    'convertFormat' => true,
    'initRangeExpr' => true,
    'pluginOptions' => [
        'autoApply' => true,
        'ranges' => [
            Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
            Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
            Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
            Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
        ],
        'locale' => [
            'format' => 'd-m-Y',
        ],
        'opens' => 'right',
        ],

    ]);
   ?>
   </div>    
	<?php if(Yii::$app->controller->action->id === 'tax-collected') : ?>
	<div class="pull-right  m-r-20">
		<div class="schedule-index">
			<div class="royalty-report-summarize">
				<?= $form->field($model, 'summarizeResults')->checkbox(['data-pjax' => true]); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
    <div class="col-md-5 form-group m-t-20">
        <?php echo Html::submitButton(Yii::t('backend', 'Go'), ['class' => 'btn btn-primary']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
