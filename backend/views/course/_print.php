<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = $model->id;
?>
<section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <span class="logo-lg"><b>Arcadia</b>SMW</span>
          <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-6 invoice-col">
          <div class="invoice-print-address">
          From
        <address>
          <strong>Arcadia Music Academy ( <?= $model->teacher->userLocation->location->name; ?>)</strong><br>
          <?= $model->teacher->userLocation->location->address; ?> <br>
          <?= $model->teacher->userLocation->location->city->name?>, <?= $model->teacher->userLocation->location->province->name;?><br>
          Phone:  <?= $model->teacher->userLocation->location->phone_number;?><br>
          Email: <?= $model->teacher->userLocation->location->email;?>
        </address>
      </div>
    </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
          <div class="invoice-print-address">
        Teacher
        <address>
            <strong><?= $model->teacher->publicIdentity; ?></strong><br>
            <?php if (!empty($model->teacher->primaryAddress)): ?>
                <?= $model->teacher->primaryAddress->address; ?><br>
                <?= $model->teacher->primaryAddress->city->name; ?>,<?= $model->teacher->primaryAddress->province->name; ?><br>
            <?php endif; ?>
            <?php if (!empty($model->teacher->phoneNumber)): ?>
                Phone: <?= $model->teacher->phoneNumber->number; ?><br>
            <?php endif; ?>
            <?php if (!empty($model->teacher->email)): ?>
                Email: <?= $model->teacher->email; ?>
            <?php endif; ?>
        </address>
      </div>
      </div>
      <!-- /.col -->
      <div class="col-sm-2 invoice-col">
          <b><?= $model->program->name; ?></b><br/>
          <b><?= Yii::$app->formatter->asDate($model->startDate); ?>-<?= Yii::$app->formatter->asDate($model->endDate); ?></b><br>
        <br>
        <b>Duration:</b>
        <?php
        $length = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->duration);
        echo $length->format('H:i');
        ?> <br>
        <b>Time:</b>
         <?php
        $fromTime = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->fromTime);
        echo $fromTime->format('h:i A');
        ?><br>
       
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
<div class="row-fluid p-10">      
    <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'summary' => '',    
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