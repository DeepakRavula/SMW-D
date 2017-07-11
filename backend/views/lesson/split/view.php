<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'split-lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $splitDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    return !empty($data->unit) ? $data->unit : null;
                },
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    return $data->getStatus();
                },
            ]
        ]
    ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>

