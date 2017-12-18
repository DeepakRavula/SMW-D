<?php

use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Teacher Availabilities',
	'withBorder' => true,
])
?>
     <?php
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
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
      'tableOptions' => ['class' => 'table table-bordered table-more-condensed'],
      'headerRowOptions' => ['class' => 'bg-light-gray'],
	'summary' => '',
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
<?php LteBox::end()?>
  