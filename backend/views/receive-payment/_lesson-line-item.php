<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;
use kartik\daterange\DateRangePicker;

?>


<?php 
   if ($searchModel->showCheckBox) {
    $columns = [
        [
        'class' => 'yii\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:30px;'],
        'checkboxOptions' => function($model, $key, $index, $column) {
            return ['checked' => true];
        }
        ]
    ];
} else {
    $columns = [];
       
}
        array_push($columns,[
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'label' => 'Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDateTime($data->date);
            }
        ]);
        array_push($columns,[
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left'],
            'attribute' => 'royaltyFree',
            'label' => 'Program',
            'value' => function ($model) {
                return $model->course->program->name;
            }
        ]);
        array_push($columns,[
            'headerOptions' => ['class' => 'text-left'],
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            }
        ]);
        array_push($columns,[
            'label' => 'Amount',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->netPrice);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);
        array_push($columns,[
            'label' => 'Balance',
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency($data->getOwingAmount($data->enrolment->id));
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right']
        ]);
?>
<?php Pjax::Begin(['id' => 'lesson-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'lesson-line-item-grid',
        'dataProvider' => $lessonLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
<?php Pjax::end(); ?>

