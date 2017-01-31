<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Classrooms';

$addButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['action-button'] = $addButton;
?>

<div class="class-room-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
		'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            'name',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
