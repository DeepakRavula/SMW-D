<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Release Notes';
$this->params['action-button'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i> Create', ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="release-notes-index "> 
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'notes:raw',
            'date:date',
            'user.publicIdentity',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?> 

</div> 