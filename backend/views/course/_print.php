<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
?>

<div class="invoice-view p-10">
    <div class="row-fluid user-details-wrapper">
        <div class="row-fluid p-10">
            <h4 class="m-0 f-w-400"><strong><?= 'Lessons'?> </strong></h4>
        </div> 
        <div class="col-md-2 pull-left">
            <?= 'Teacher Name: ' ?> <?= $model->teacher->publicIdentity; ?>
        </div>
        <div class="col-md-2 pull-right">
            <?= 'Program Name: ' ?> <?= $model->program->name; ?>
        </div>    	   
    </div>
    <div class="row-fluid p-10">
        <div class="col-md-2 pull-left">
            <?= 'Start Date: ' ?> <?= Yii::$app->formatter->asDate($model->startDate); ?>
        </div> 
        <div class="col-md-2 pull-left">
            <?= 'End Date: ' ?> <?= Yii::$app->formatter->asDate($model->endDate); ?>
        </div>        
        <div class="col-md-2 pull-right">
            <?= 'Time: ' ?> <?php 
            $fromTime = \DateTime::createFromFormat('H:i:s', $model->fromTime);
            echo $fromTime->format('h:i A');?>	
        </div>
        <div class="col-md-2 pull-right">
            <?= 'Durartion: ' ?> <?php 
            $length = \DateTime::createFromFormat('H:i:s', $model->duration);
            echo $length->format('H:i'); ?>
        </div>  
    </div> 
    <div class="row-fluid p-10">      
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
		'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
            [
				'label' => 'Teacher Name',
				'value' => function($data) {
					return ! empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
                },
			],
			[
				'label' => 'Status',
				'value' => function($data) {
					$status = null;
					if (!empty($data->status)) {
					return $data->getStatus();
					}
				return $status;
                },
			],
        ],
	    ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>