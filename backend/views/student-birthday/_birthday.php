<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Student;

?>
<div class="grid-row-open"> 
    <?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
                [
                'attribute' => 'first_name',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !(empty($data->first_name)) ? $data->first_name : null;
                },
            ],
            'last_name',
                [
                'attribute' => 'birth_date',
                'label' => 'Date',
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->birth_date);

                    return !empty($date) ? $date : null;
                },
            ],
                [
                'attribute' => 'customer_id',
                'label' => 'Customer Name',
                'value' => function ($data) {
                    $fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;

                    return $fullName;
                },
            ],
        ],
    ]);

    ?>

<?php yii\widgets\Pjax::end(); ?>
</div>