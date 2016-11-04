<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vacations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vacation-index">


    <p>
        <?php echo Html::a('Create Vacation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'studentId',
            'fromDate',
            'toDate',
            'isConfirmed',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
