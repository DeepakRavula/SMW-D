<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-type-index">


    <p>
        <?php echo Html::a('Create Item Type', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
