<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Exam Results';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exam-result-index">


    <p>
        <?php echo Html::a('Create Exam Result', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'studentId',
            'date',
            'mark',
            'level',
            // 'program',
            // 'type',
            // 'teacherId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
