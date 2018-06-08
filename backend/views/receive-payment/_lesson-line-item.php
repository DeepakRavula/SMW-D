<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;
use kartik\daterange\DateRangePicker;

?>

<div class="pull-right">
    <?= DateRangePicker::widget([
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
                'format' => 'M d,Y'
            ],
            'opens' => 'right'
        ]
    ]); ?>
</div>
<?php 
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;']
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                return $data->date;
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'royaltyFree',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ],
        [
            'label' => 'Amount',
            'value' => function ($data) {
                return $data->amount;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
        [
            'label' => 'Payment',
            'value' => function ($data) {
                return $data->amount;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]
    ];
?>
<?php Pjax::Begin(['id' => 'invoice-view-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'line-item-grid',
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        //'tableOptions' => $tableOption,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
<?php Pjax::end(); ?>

<script>
    $(document).on("click", "input[type='checkbox']", function(event) {
        event.stopPropagation();
    });
    $(document).off("change", "input[type='checkbox']").on("change", "input[type='checkbox']", function() {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        if (selectedRows.length >= 2) {
            $('.apply-discount').text('Edit Discounts...');
            $('.edit-tax').text('Edit Taxes...');
        } else {
            $('.apply-discount').text('Edit Discount...');
            $('.edit-tax').text('Edit Tax...');
        }
    });
</script>