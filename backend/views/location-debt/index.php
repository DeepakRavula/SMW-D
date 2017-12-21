<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Location Debts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-debt-index">


    <p>
        <?php echo Html::a('Create Location Debt', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'locationId',
            'type',
            'value',
            'since',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
