<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Methods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-methods-index">


    <p>
        <?php echo Html::a('Create Payment Methods', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => 'Serial No.',
            ],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
