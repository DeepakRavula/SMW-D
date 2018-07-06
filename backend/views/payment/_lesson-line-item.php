<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Lesson;
use kartik\daterange\DateRangePicker;

?>


<?php 
    $columns = [
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
                return Yii::$app->formatter->asCurrency($data->netPrice);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
	[
            'label' => 'Balance',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->getOwingAmount($data->enrolment->id));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ],
    ];
    
    if ($canEdit) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'label' => 'Payment',
            'value' => function ($data) use ($model) {
                return Html::textInput('', round($data->getPaidAmount($model->id), 2), [
                    'class' => 'payment-amount text-right'
                ]); 
            },
            'attribute' => 'new_activity',
            'format' => 'raw'
        ]);
    }
?>

    <?php Pjax::Begin(['id' => 'lesson-listing', 'timeout' => 6000]); ?>
        <?= GridView::widget([
            'id' => 'lesson-grid',
            'dataProvider' => $lessonDataProvider,
            'columns' => $columns,
            'summary' => false,
            'emptyText' => false,
            'rowOptions' => ['class' => 'line-items-value lesson-line-items'],
            'options' => ['class' => 'col-md-12'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
        ]); ?>
    <?php Pjax::end(); ?>