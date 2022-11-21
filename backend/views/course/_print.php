<?php

use common\models\Enrolment;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
?>
<section class="invoice">

    <!-- title row -->
 <?php
   echo $this->render('/print/_header', [
       'courseModel'=>$model,
       'userModel'=>$model->teacher,
       'locationModel'=>$model->teacher->userLocation->location,
]);
   ?>
    <!-- /.row -->
<div class="row-fluid p-10">      
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Teacher Name',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);

                    return !empty($date) ? $date : null;
                },
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
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
    <!-- Table row -->
    
    <!-- /.row -->
  </section>