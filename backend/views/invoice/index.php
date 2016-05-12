<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">




    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lesson_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	<p>
        <?php echo Html::a('Create Invoice', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
