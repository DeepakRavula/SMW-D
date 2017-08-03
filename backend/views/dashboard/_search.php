<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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
		'id' => 'dashboard-search-form',
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
                'locale' => [
                    'format' => 'M d,Y',
                ],
                'opens' => 'left',
                ],

            ]);
           ?>
        </div>
	   <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['name' => 'dashboard-apply-button', 'class' => 'btn btn-primary']) ?>
	</div>
    <?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
$("#dashboard-search-form").on("submit", function () {
            var dateRange = $('#dashboardsearch-daterange').val();
            $.pjax.reload({container: "#dashboard", replace: false, timeout: 6000, data: $(this).serialize()});
            return false;
        });
    });
</script>