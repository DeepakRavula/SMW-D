<?php

use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
?>
<div class="clearfix"></div>
<div class="col-md-10">
<div class="box box-default collapsed-box">
  <div class="box-header with-border">
    <h3 class="box-title">Teacher Availabilities</h3>
    <div class="box-tools">
      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body ">
     <?php
    $locationId = Yii::$app->session->get('location_id');
    $query = TeacherAvailability::find()
    ->joinWith('userLocation')
    ->where(['user_id' => $courseModel->teacherId, 'location_id' => $locationId]);
    $teacherAvailabilityDataProvider = new ActiveDataProvider([
    'query' => $query,
    ]);
  ?>
  <?php
    echo GridView::widget([
      'dataProvider' => $teacherAvailabilityDataProvider,
      'options' => ['class' => 'col-md-5'],
      'tableOptions' => ['class' => 'table table-bordered table-more-condensed'],
      'headerRowOptions' => ['class' => 'bg-light-gray'],
      'columns' => [
          [
            'label' => 'Day',
            'value' => function ($data) {
            if (!empty($data->day)) {
            $dayList = TeacherAvailability::getWeekdaysList();
            $day   = $dayList[$data->day];
            return !empty($day) ? $day : null;
          }
            return null;
          },
          ],
        [
          'label' => 'From Time',
          'value' => function ($data) {
          return !empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
          },
        ],
        [
          'label' => 'To Time',
          'value' => function ($data) {
          return !empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
          },
        ],
      ],
    ]);
  ?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</div><!-- /.box -->
<div class="clearfix"></div>