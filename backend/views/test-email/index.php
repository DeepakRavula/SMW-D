<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Email';
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="student-index">  
<?php yii\widgets\Pjax::begin(['id' => 'test-email']); ?>
<?php
echo AdminLteGridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'ID',
            'value' => function ($data) {
                return $data->id;
            },
        ],
        [
            'label' => 'Email',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->email;
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
    </div>