<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teacher Rooms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-room-index">


    <p>
        <?php echo Html::a('Create Teacher Room', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'day',
            'classroomId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
