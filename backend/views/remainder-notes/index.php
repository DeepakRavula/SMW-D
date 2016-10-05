<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Remainder Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remainder-notes-index">


    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'text:ntext',
            'date',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>

</div>
