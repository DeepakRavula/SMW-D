<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
?>
<style>
.table{
    margin-bottom: 0;
}
    .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
        border-top: 0;
    }
</style>
<div class="invoice-view p-10">
        <h4 class="f-w-400 p-l-10"><strong><?= 'Lessons'?> </strong></h4>
        <table class="table">
            <tbody>
                <tr>
                    <td><strong><?= 'Teacher Name: ' ?></strong> <?= $model->teacher->publicIdentity; ?></td>
                    <td><strong><?= 'Program Name: ' ?></strong> <?= $model->program->name; ?></td>
                    <td><strong><?= 'Time: ' ?></strong> 
                        <?php 
                            $fromTime = \DateTime::createFromFormat('H:i:s', $model->fromTime);
                            echo $fromTime->format('h:i A');
                        ?>
                    </td>
                    
                </tr>
                <tr>
                    <td>
                        <strong><?= 'Durartion: ' ?></strong>
                        <?php 
                            $length = \DateTime::createFromFormat('H:i:s', $model->duration);
                            echo $length->format('H:i'); 
                        ?>
                    </td>
                    <td><strong><?= 'Start Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->startDate); ?></td>
                    <td><strong><?= 'End Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->endDate); ?></td>
                </tr>
            </tbody>
        </table>
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