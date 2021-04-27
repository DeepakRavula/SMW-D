<?php

use common\models\ItemCategory;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="user-search">

<?php   $categories = ArrayHelper::map(ItemCategory::find()
                        ->notDeleted()
                        ->all(), 'id', 'name')?>
    <?php $form = ActiveForm::begin([
        'id' => 'item-category-search-form',
        'action' => ['report/item-category'],
        'method' => 'get',
    ]); ?>
    <div class="form-group">
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
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d,Y',
                ],
                'opens' => 'right',
                ],

            ]);
           ?>
    </div>
    <div class="form-group m-t-10">
            <?= $form->field($model, 'category')->widget(Select2::classname(), [
                'data' => $categories,
                'options' => ['placeholder' => 'Category'],
                'hashVarLoadPosition' => View::POS_READY
            ])->label(false)
            ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).off('change', '#invoicelineitemsearch-daterange,#invoicelineitemsearch-category').on('change', '#invoicelineitemsearch-daterange,#invoicelineitemsearch-category', function() {
        $("#item-category-search-form").submit();
    });
</script>
