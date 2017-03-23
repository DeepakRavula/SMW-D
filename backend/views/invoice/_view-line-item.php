<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;
use common\models\InvoiceLineItem;
use kartik\switchinput\SwitchInput;
use common\models\ItemType;
use yii\widgets\ActiveForm;

?>
<?php if ($searchModel->show) {
    $columns = [
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->itemType->itemCode;
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:50px;'],
            'attribute' => 'isRoyalty',
            'label' => 'R',
            'value' => function ($model) {
                return $model->isRoyalty ? 'Yes' : 'No';
            },
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                return [
                    'header' => 'Royalty',
                    'size' => 'md',
                    'placement' => 'top',
                    'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    'options' => [
                        'pluginOptions' => [
                            'handleWidth' => 60,
                            'onText' => 'Yes',
                            'offText' => 'No',
                        ],
                    ],
                    'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
                ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                return [
                   'header' => 'Description',
                   'size' => 'md',
                               'placement' => 'top',
                   'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                   'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
               ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'attribute' => 'discount',
            'value' => function ($model) {
                if ((int) $model->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                    return Yii::$app->formatter->format($model->discount, ['currency']);
                } else {
                    return $model->discount . '%';
                }
            },
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                return [
                    'header' => 'Discount',
                    'size' => 'md',
                    'placement' => 'left',
                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                    'options' => [
                        'value' => $model->discount,
                    ],
                    'afterInput' => function ($form, $widget) use ($model, $index) {
                        echo $form->field($model, "[{$index}]discountType")->widget(SwitchInput::classname(),
                            [
                            'name' => 'discountType',
                            'pluginOptions' => [
                                'handleWidth' => 60,
                                'onText' => '%',
                                'offText' => '$',
                            ],
                        ])->label(false);
                    },
                    'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
                    'pluginEvents' => [
                        'editableSuccess' => 'invoice.onEditableGridSuccess',
                    ],
                ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'tax_status',
            'refreshGrid' => true,
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;'],
            'editableOptions' => function ($model, $key, $index) {
                return [
                    'header' => 'Tax Status',
                    'size' => 'md',
                    'placement' => 'top',
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => ArrayHelper::map(TaxStatus::find()->all(), 'id', 'name'),
                    'options' => [
                        'prompt' => 'Select Tax'
                    ],
                    'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
                    'pluginEvents' => [
                        'editableSuccess' => 'invoice.onEditableGridSuccess',
                    ],
                ];
            },
        ],
        [
            'label' => 'Cost',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function($data) {
                $cost = 0;
                $itemTypes = [ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON, ItemType::TYPE_GROUP_LESSON];
                if(in_array($data->item_type_id,$itemTypes)) {
                    if((int)$data->item_type_id === ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON) {
                        $cost = !empty($data->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate) ? $data->paymentCycleLesson->lesson->teacher->teacherPrivateLessonRate->hourlyRate : null;
                    } else {
                        $cost = !empty($data->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate) ? $data->paymentCycleLesson->lesson->teacher->teacherGroupLessonRate->hourlyRate : null;
                    }
                }
                return $cost;
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'amount',
            'label' => 'Price',
            'refreshGrid' => true,
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'enableSorting' => false,
            'editableOptions' => function ($model, $key, $index) {
                if ($model->isOpeningBalance()) {
                    $model->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
                }
                return [
                    'header' => 'Price',
                    'size' => 'md',
                    'placement' => 'left',
                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                    'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
                    'pluginEvents' => [
                        'editableSuccess' => 'invoice.onEditableGridSuccess',
                    ],
                ];
            },
        ],
        [
            'class' => kartik\grid\ActionColumn::className(),
            'template' => '{delete}',
            'headerOptions' => ['class' => 'text-left'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['invoice-line-item/delete', 'id' => $model->id], [
              'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
                  ]);
                },
            ],
        ],
    ];
} else {
    $columns = [
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->itemType->itemCode;
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                return [
                   'header' => 'Description',
                   'size' => 'md',
                               'placement' => 'top',
                   'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                   'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
               ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'amount',
            'label' => 'Price',
            'refreshGrid' => true,
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'enableSorting' => false,
            'editableOptions' => function ($model, $key, $index) {
                if ($model->isOpeningBalance()) {
                    $model->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
                }
                return [
                    'header' => 'Price',
                    'size' => 'md',
                    'placement' => 'left',
                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                    'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
                    'pluginEvents' => [
                        'editableSuccess' => 'invoice.onEditableGridSuccess',
                    ],
                ];
            },
        ],
        [
            'class' => kartik\grid\ActionColumn::className(),
            'template' => '{delete}',
            'headerOptions' => ['class' => 'text-left'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['invoice-line-item/delete', 'id' => $model->id], [
              'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
                  ]);
                },
            ],
        ],
    ];
}?>

<?= \kartik\grid\GridView::widget([
    'dataProvider' => $invoiceLineItemsDataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'line-item-listing',
        ],
    ],
    'columns' => $columns,
    'responsive' => false,
]); ?>
