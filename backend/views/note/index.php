<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-index">


    <p>
        <?php echo Html::a('Create Note', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'instanceId',
            'instanceType',
            'content:ntext',
            'createdUserId',
            // 'createdOn',
            // 'updatedOn',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
