<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PrivateLessonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Private Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="private-lesson-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?php echo Html::a('Create Private Lesson', ['create'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'lessonId',
            'expiryDate',
            'isElgible',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
