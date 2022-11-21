<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\Student;
use common\models\UserProfile;
use common\models\Course;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>


<?php  ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Schedule History',
    'withBorder' => true,
])
?>
<div class="enrolment-schedule-history-index">
    <?php
    $columns = [
	[
	    'label' => 'Date',
	    'value' => function ($data) {
		    return Yii::$app->formatter->asDate($data->startDate) . ' - ' . Yii::$app->formatter->asDate($data->endDate);
	    },
	],
	[
	    'label' => 'Day',
	    'value' => function ($data) {
			$dayList = Course::getWeekdaysList();
		    return $dayList[$data->day];
	    },
	],
	[
	    'label' => 'Time',
	    'value' => function ($data) {
		    return Yii::$app->formatter->asTime($data->fromTime);
	    },
	],
	[
	    'label' => 'Duration',
	    'value' => function ($data) {
		    $lessonDuration = (new \DateTime($data->duration))->format('H:i');
		    return $lessonDuration;
	    },
	],
	[
	    'label' => 'Teacher',
	    'value' => function ($data) {
		    return $data->teacher->publicIdentity;
	    },
	],
    ];
    ?>
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-schedule-history-index', 'timeout' => 6000,]); ?>
	<?php echo GridView::widget([
	    'dataProvider' => $scheduleHistoryDataProvider,
	    'options' => ['id' => 'enrolment-schedule-history-index', 'class' => 'col-md-12'],
	    'tableOptions' => ['class' => 'table table-condensed'],
	    'headerRowOptions' => ['class' => 'bg-light-gray'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);
	
	?>
	<?php yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end()?>