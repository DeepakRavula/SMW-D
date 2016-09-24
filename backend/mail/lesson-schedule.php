<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>

Dear <?php echo Html::encode($toName) ?>,<br>
   <div class="invoice-view p-10">
    <div class="row-fluid user-details-wrapper">
        <div class="row-fluid p-10">
            <h4 class="m-0 f-w-400"><strong><?= 'Please find the lesson schedule for the program you enrolled on ' . Yii::$app->formatter->asDate($model->course->startDate) ?> </strong></h4>
        </div> 
        <div class="col-md-2 pull-left">
            <?= 'Teacher Name: ' ?> <?= $model->course->teacher->publicIdentity; ?>
        </div>
        <div class="col-md-2 pull-right">
            <?= 'Program Name: ' ?> <?= $model->course->program->name; ?>
        </div>    	   
    </div>
    <div class="row-fluid p-10">
        <div class="col-md-2 pull-left">
            <?= 'Start Date: ' ?> <?= Yii::$app->formatter->asDate($model->course->startDate); ?>
        </div> 
        <div class="col-md-2 pull-left">
            <?= 'End Date: ' ?> <?= Yii::$app->formatter->asDate($model->course->endDate); ?>
        </div>        
        <div class="col-md-2 pull-right">
            <?= 'Time: ' ?> <?php 
            $fromTime = \DateTime::createFromFormat('H:i:s', $model->course->fromTime);
            echo $fromTime->format('h:i A');?>	
        </div>
        <div class="col-md-2 pull-right">
            <?= 'Durartion: ' ?> <?php 
            $length = \DateTime::createFromFormat('H:i:s', $model->course->duration);
            echo $length->format('H:i'); ?>
        </div>  
    </div> 
    <div class="clearfix"></div>
    <div class="row-fluid p-10">
        <h4 class="m-0 f-w-400"><strong><?= 'Schedule of Lessons' ?> </strong></h4>
    </div>      
    <div class="row-fluid p-10">      
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,		
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
	    'summary' => '',
        'columns' => [            
			[				
				'value' => function($data) {
					$lessonDate =  \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                    $date = $lessonDate->format('l, F jS, Y @ g:i a');    
					return ! empty($date) ? $date : null;
                },
			],			
        ],
	    ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    </div>    
</div>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>