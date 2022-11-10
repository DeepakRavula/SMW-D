<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TeacherAvailabilitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teacher Availabilities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-availability-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?php echo Html::a('Create Teacher Availability', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => 'Serial No.',
            ],

            'id',
            'teacher_id',
            'location_id',
            'day',
            'from_time',
            // 'to_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
