<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;
use yii\helpers\Html;

?>

<?php
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'header' => Html::checkBox('selection_all', false, [
                'class' => 'select-on-check-all'
            ]),
            'contentOptions' => ['style' => 'width:30px;'],
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['checked' => true];
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDateTime($data->lineItem->lesson->date);
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'royaltyFree',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->lineItem->lesson->course->program->name;
            }
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->lineItem->lesson->teacher->publicIdentity;
            }
        ],
        [
            'label' => 'Amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->balance);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
        [
            'label' => 'Payment',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->balance);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]
    ];
?>
<?php Pjax::Begin(['id' => 'invoice-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'invoice-line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
<?php Pjax::end(); ?>

