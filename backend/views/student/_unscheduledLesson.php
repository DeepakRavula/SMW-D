<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="private-lesson-index">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index',
    'timeout' => 6000,]); ?>
    <?php $columns = [
           
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
			[
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->customer->phoneNumber->number) ? $data->course->enrolment->student->customer->phoneNumber->number : null;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);

                    return !empty($date) ? $date : null;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    if (!empty($data->privateLesson->expiryDate)) {
                        $date = Yii::$app->formatter->asDate($data->privateLesson->expiryDate);
                    }

                    return !empty($date) ? $date : null;
                },
            ],
			[
                'label' => 'Status',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px;'],
                'value' => function ($data) {
                    return $data->isExploded ? Html::a('<i class="fa fa-code-fork fa-lg"></i>', null) : null;
                }
            ],
        ];

    ?>
    <div class="grid-row-open">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>
