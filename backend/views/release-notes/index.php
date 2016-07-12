<?php

use yii\helpers\Html; 
use yii\grid\GridView; 

/* @var $this yii\web\View */ 
/* @var $dataProvider yii\data\ActiveDataProvider */ 

$this->title = 'Release Notes';
$this->params['subtitle'] = Html::a('Create Release Notes', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title; 
?> 
<div class="release-notes-index p-10"> 
    <?php echo GridView::widget([ 
        'dataProvider' => $dataProvider, 
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'], 
            'notes:raw',
            'date:date',
            'user.publicIdentity',
            ['class' => 'yii\grid\ActionColumn'], 
        ], 
    ]); ?> 

</div> 