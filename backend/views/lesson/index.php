<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">


    <p>
        <?php echo Html::a('Create Lesson', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'student_id',
            'teacher_id',
            'program_id',
            'rate',
            // 'quantity',
            // 'commencement_date',
            // 'invoiced_id',
            // 'location_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
