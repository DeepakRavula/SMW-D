<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\TeacherAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */

//$this->title = $model->teacher->publicIdentity;
//$this->params['breadcrumbs'][] = ['label' => 'Teacher Availabilities', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-availability-view">

    <?php 
        $dayList = TeacherAvailability::getWeekdaysList();
        $day = $dayList[$model->day];
        $fromTime = Yii::$app->formatter->asTime($model->from_time);
        $toTime = Yii::$app->formatter->asTime($model->to_time);
    ?>
    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
           [
                'label' => 'Day',
                'value' => !empty($day) ? $day : null,
            ],
            [
                'label' => 'From Time',
                'value' => !empty($fromTime) ? $fromTime : null,
            ],
            [
                'label' => 'To Time',
                'value' => !empty($toTime) ? $toTime : null,
            ],
        ],
    ]) ?>

    <p>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
