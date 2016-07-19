<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupEnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Enrolments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-enrolment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Group Enrolment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'course_id',
            'student_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
