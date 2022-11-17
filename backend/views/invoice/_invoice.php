<?php

use yii\grid\GridView;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">

<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Student Name',
                'value' => function ($data) {
                    return !empty($data->enrolmentScheduleDay->enrolment->student->fullName) ? $data->enrolmentScheduleDay->enrolment->student->fullName : null;
                },
            ],
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->enrolmentScheduleDay->enrolment->qualification->program->name : null;
                },
            ],
            [
                'label' => 'Program Price',
                'value' => function ($data) {
                    return !empty($data->enrolmentScheduleDay->enrolment->qualification->program->rate) ? $data->enrolmentScheduleDay->enrolment->qualification->program->rate : null;
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    return !empty($data->enrolmentScheduleDay->duration) ? $data->enrolmentScheduleDay->duration : null;
                },
            ],
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

