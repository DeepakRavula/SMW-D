<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-view">



    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
				'label' => 'Student Name',
				'value' => ! empty($model->enrolmentScheduleDay->enrolment->student->fullName) ? $model->enrolmentScheduleDay->enrolment->student->fullName : null,
			],
			[
				'label' => 'Program Name',
				'value' => ! empty($model->enrolmentScheduleDay->enrolment->qualification->program->name) ? $model->enrolmentScheduleDay->enrolment->qualification->program->name : null,
			],
			[
				'label' => 'Status',
				'value' => $model->status($model),
			],
			[
				'label' => 'Date',
				'value' => ! empty(date("d-m-y", strtotime($model->date))) ? date("d-m-y", strtotime($model->date)) : null,
			],
        ],
    ]) ?>
    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
