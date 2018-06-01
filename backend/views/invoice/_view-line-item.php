<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;

    if ($searchModel->isWeb) {
        $tableOption = ['class' => 'table table-condensed'];
        $columns = [
            [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;']
            ]
        ];
    } else {
        $tableOption = ['class' => 'table table-condensed m-0', 'style'=>'width:100%; text-align:left'];
        $columns = [
            [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->code;
            }
            ]
        ];
    }
    if ($searchModel->toggleAdditionalColumns) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:70px;'],
            'attribute' => 'royaltyFree',
            'label' => 'Royalty Free',
            'value' => function ($model) {
                return $model->royaltyFree ? 'Yes' : 'No';
            }
        ]);
    }
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'label' => 'Description',
        'value' => function ($data) {
            return $data->description;
        }
    ],
    [
        'label' => 'Qty',
        'value' => function ($data) {
            return $data->unit;
        },
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;']
    ]);
    if ($searchModel->toggleAdditionalColumns) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'format' => 'currency',
            'attribute' => 'discount',
            'value' => function ($model) {
                return Yii::$app->formatter->asDecimal($model->discount);
            }
        ],
        [
            'attribute' => 'tax_status',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;']
        ],
        [
            'label' => 'Cost',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->cost);
            }
        ]);
    }
    if ($model->isProformaInvoice()) {
        array_push($columns, [
            'label' => 'Payment',
            'format' => 'currency',
            'value' => function ($data) {
                $amount = 0;
                if (!$data->isGroupLesson()) {
                    if (!$data->invoice->isDeleted() && $data->proFormaLesson) {
                        if ($data->proFormaLesson->isCanceled()) {
                            $lessons = Lesson::find()
                                ->descendantsOf($data->proFormaLesson->id)
                                ->all();
                            foreach ($lessons as $lesson) {
                                $amount += $lesson->getCreditAppliedAmount($lesson->enrolment->id);
                            }
                        } else {
                            $amount = $data->proFormaLesson->getCreditAppliedAmount($data->proFormaLesson->enrolment->id) ?? 0;
                        }
                    }
                } else {
                    $amount = $data->lesson->getCreditAppliedAmount($data->enrolment->id) ?? 0;
                }
                return Yii::$app->formatter->asDecimal($amount);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;']
        ]);
    }
    array_push($columns, [
        'label' => 'Price',
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        'value' => function ($data) {
            return Yii::$app->formatter->asDecimal($data->netPrice);
        }
    ]);
Pjax::Begin(['id' => 'invoice-view-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => $tableOption,
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