<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
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
?>
<?php Pjax::Begin(['id' => 'lesson-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'lesson-grid',
        'dataProvider' => $lessonDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
<?php Pjax::end(); ?>