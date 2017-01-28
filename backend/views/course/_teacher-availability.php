<?php

use yii\grid\GridView;
use common\models\TeacherAvailability;
use yii\helpers\Html;

?>
<?php yii\widgets\Pjax::begin() ?>
<?php

echo GridView::widget([
    'dataProvider' => $teacherDataProvider,
    'options' => ['class' => 'col-md-5'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Day',
            'value' => function ($data) {
                if (!empty($data->day)) {
                    $dayList = TeacherAvailability::getWeekdaysList();
                    $day = $dayList[$data->day];

                    return !empty($day) ? $day : null;
                }

                return null;
            },
        ],
        [
            'label' => 'From Time',
            'value' => function ($data) {
                return !empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
            },
        ],
        [
            'label' => 'To Time',
            'value' => function ($data) {
                return !empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
            },
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
