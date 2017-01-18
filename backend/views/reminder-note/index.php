<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reminder Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-notes-index p-10">


    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'notes:raw',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>

</div>
