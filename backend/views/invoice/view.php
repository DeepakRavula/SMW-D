<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-view">



    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
				'label' => 'Program Name',
				'value' => ! empty($model->lesson->enrolmentScheduleDay->enrolment->qualification->program->name) ? $model->lesson->enrolmentScheduleDay->enrolment->qualification->program->name : null,
			],
            'amount',
			[
				'label' => 'Date',
				'value' => ! empty(date("d-m-y", strtotime($model->date))) ? date("d-m-y", strtotime($model->date)) : null,
			],
			[
				'label' => 'Status',
				'value' => $model->status($model),
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
