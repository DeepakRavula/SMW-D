<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */

?>
<style>
  .e1Div{
    right: 0;
    top: -49px;
  }
  .e1Div > .form-group > input {
    width: 180px;
  }
</style>
    <?php $form = ActiveForm::begin([
		'id' => 'birthday-search-form',
        'action' => ['index'],
        'method' => 'get'
    ]); ?>
    <div class="e1Div form-inline">
        <div class="form-group">
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
                'opens' => 'left',
                ],

            ]);
           ?>
        </div>
	   <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary']) ?>
	</div>
    <?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
$("#birthday-search-form").on("submit", function () {
		var dateRange = $('#birthday-daterange').val();
		$.pjax.reload({container: "#birthday-listing", replace: false, timeout: 6000, data: $(this).serialize()});
		return false;
	});
});
</script>