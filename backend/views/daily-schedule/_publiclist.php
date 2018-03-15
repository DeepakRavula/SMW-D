<?php

use yii\grid\GridView;

?>

<?php yii\widgets\Pjax::begin(['id' => 'schedule-listing']); ?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'emptyText' => false,
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['style' => 'font-weight:bold; color:white; font-size: 16px;'
            . 'background-color:' . $model->getColorCode() . ';'];
    },
    'tableOptions' => ['class' => 'table table-condensed'],
    'options' => [
        'class' => 'daily-schedule',
    ],
    'columns' => [
            [
            'label' => 'Start time',
            'value' => function ($data) {
                return Yii::$app->formatter->asTime($data->date);
            },
        ],
            [
            'label' => 'Student',
            'value' => function ($data) {
                $student = '-';
                if ($data->course->program->isPrivate()) {
                    $student = $data->enrolment->student->fullName;
                }
                return $student;
            },
        ],
            [
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->teacher->publicIdentity;
            },
        ],
            [
            'label' => 'Program',
            'value' => function ($data) {
                return $data->course->program->name;
            },
        ],
            [
            'label' => 'Classroom',
            'value' => function ($data) {
                return !empty($data->classroomId) ? $data->classroom->name : null;
            },
        ],
    ]
]);
?>
<?php yii\widgets\Pjax::end(); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
$(document).ready(function () {
    var locationId = $.cookie('locationId');
    if(locationId) {
        $('#locationId').val(locationId);
    }
    $(document).on('change', '#locationId', function(){
        $.cookie('locationId', $(this).val());
        $("#schedule-search").submit();
    });
    $(document).on('submit', '#schedule-search', function () {
        $.pjax.reload({container: "#schedule-listing", replace: false, timeout: 6000, data: $(this).serialize()});
        return false;
    });
    (function(){
        $.pjax.reload({container: "#schedule-listing", replace: false, timeout: 6000});
        setTimeout(arguments.callee, 60000);
    })();
});
</script>
