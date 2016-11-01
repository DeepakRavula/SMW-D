<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\EnrolmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enrolments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enrolment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?php echo Html::a('Create Enrolment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'courseId',
            'studentId',
            'isDeleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
