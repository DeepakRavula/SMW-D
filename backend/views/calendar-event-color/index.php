<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calendar Event Colors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-event-color-index">


    <p>
        <?php echo Html::a('Create Calendar Event Color', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'code',
            'cssClass',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
