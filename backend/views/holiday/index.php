<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Holidays';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Holiday', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
