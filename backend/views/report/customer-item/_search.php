<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id; ?>
<?php $url = Url::to(['customer-items-report/index', 'id' => $model->customerId]); ?>
    <?php $form = ActiveForm::begin([
        'id' => 'customer-item-search-form',
        'action' => $url,
        'method' => 'get',
    ]); ?>
    <div class="form-group">
        <div class="report-header-search">
        <div class="col-md-5">
            <?php echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
		'options' => [
                    'class' => 'form-control',
                    'readOnly' => true
                ],
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'This Year') => ["moment().startOf('year')", "moment().endOf('year')"],
                        Yii::t('kvdrp', 'Last Year') => ["moment().subtract(1, 'year').startOf('year')", "moment().subtract(1, 'year').endOf('year')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'left',
                    ],

                ]);
            ?>
        </div>
        <?= $form->field($model, 'isCustomerReport')->hiddenInput()->label(false); ?>
        <?= Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class'=> 'btn btn-box-tool']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

<script>
    $(document).off('change', '#invoicelineitemsearch-daterange').on('change', '#invoicelineitemsearch-daterange', function() {
      
        var url = $("#customer-item-search-form").attr('action');
        debugger;
        $("#customer-item-search-form").submit();


    });
</script>