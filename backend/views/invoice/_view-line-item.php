<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\InvoiceLineItem;

?>
<?php
$columns = [
    [
        'label' => 'Code',
        'value' => function ($data) {
            return $data->itemType->itemCode;
        },
    ],
    [
        'label' => 'R',
        'value' => function ($data) {
            return $data->isRoyalty ? 'Yes' : 'No';
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
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
        'attribute' => 'discount',
        'refreshGrid' => true,
        'headerOptions' => ['class' => 'kv-sticky-column'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => 'Discount',
                'size' => 'md',
                'placement' => 'left',
                'afterInput' => function ($form, $widget) use ($model, $index) {
                    echo $form->field($model, "[{$index}]discountType")->dropDownList([1 => '$', 2 => '%']);
                },
                'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
            ];
        },
    ],
    [
        'label' => 'Type',
        'value' => function ($data) {
            return $data->getDiscountType();
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'amount',
        'label' => 'Price',
        'refreshGrid' => true,
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
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
        'label' => 'Net Price',
        'value' => function ($data) {
            return $data->getNetPrice();
        },
    ],
    [
        'class' => kartik\grid\ActionColumn::className(),
        'template' => '{delete}',
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
]; ?>
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