<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'customer-item-search-form',
        'action' => ['report/customer-items'],
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
        <div class="col-md-5"> 
            <?=
                $form->field($model, 'customerId')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        User::find()
                            ->excludeWalkin()
                            ->customers($locationId)
                            ->notDeleted()
                            ->active()
                            ->all(),
                            'id',
                        'publicIdentity'
                    ),
                    'options' => ['placeholder' => 'Select Customer', 'class' => 'form-control'],
                         ])->label(false);
            ?>
        </div>
        <?= $form->field($model, 'isCustomerReport')->hiddenInput()->label(false); ?>
        <?= Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class'=> 'btn btn-box-tool']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

<script>
    $(document).off('change', '#invoicelineitemsearch-daterange').on('change', '#invoicelineitemsearch-daterange', function() {
        $("#customer-item-search-form").submit();
    });
</script>