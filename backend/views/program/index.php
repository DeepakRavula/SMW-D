<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Programs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'rate:currency',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
	<p>
        <?php echo Html::a('Create Program', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
