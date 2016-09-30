<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
.table{
    margin-bottom: 0;
}
    .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
        border-top: 0;
    }
</style>
Dear <?php echo Html::encode($toName) ?>,
<br><br>
   <div class="invoice-view p-10">
    <div class="row-fluid user-details-wrapper">
        <div class="row-fluid p-10">
            <h4 class="m-0 f-w-400"><strong><?= 'Please find the lesson schedule for the program you enrolled on ' . Yii::$app->formatter->asDate($model->course->startDate) ?> </strong></h4>
        </div> 
    </div>
    <table class="table">
            <tbody>
                <tr>
                    <td><strong><?= 'Teacher Name: ' ?></strong> <?= $model->course->teacher->publicIdentity; ?></td>
                    <td><strong><?= 'Program Name: ' ?></strong> <?= $model->course->program->name; ?></td>
                    <td><strong><?= 'Time: ' ?></strong> 
                        <?php 
                            $fromTime = \DateTime::createFromFormat('H:i:s', $model->course->fromTime);
                            echo $fromTime->format('h:i A');
                        ?>
                    </td>
                    
                </tr>
                <tr>
                    <td>
                        <strong><?= 'Durartion: ' ?></strong>
                        <?php 
                            $length = \DateTime::createFromFormat('H:i:s', $model->course->duration);
                            echo $length->format('H:i'); 
                        ?>
                    </td>
                    <td><strong><?= 'Start Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->startDate); ?></td>
                    <td><strong><?= 'End Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->endDate); ?></td>
                </tr>
            </tbody>
        </table>   	   
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