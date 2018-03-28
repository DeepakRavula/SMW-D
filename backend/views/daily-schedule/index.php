<?php

$this->title = 'Daily Schedule';
?>

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
            'label' => 'Program',
            'value' => function ($data) {
                return $data->course->program->name;
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
            'label' => 'Classroom',
            'value' => function ($data) {
                return !empty($data->classroomId) ? $data->classroom->name : null;
            },
        ],
	    [
            'label' => 'Status',
            'value' => function ($data) {
                return  $data->dailyScheduleStatus();
            },
        ],
    ]
]);
?>
<?php yii\widgets\Pjax::end(); ?>
<script src="https://js.pusher.com/4.2/pusher.min.js"></script>
<script type="text/javascript">
	var pusher = new Pusher('<?= env('PUSHER_KEY')?>', {
		cluster: '<?= env('PUSHER_CLUSTER')?>',
		encrypted: true
   });

    var channel = pusher.subscribe('lesson');
    channel.bind('lesson-edit', function(data) {
       $.pjax.reload({container : '#schedule-listing', timeout : 6000});
       return false;
   });
  </script>