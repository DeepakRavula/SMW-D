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
        'options' => ['class'=>'col-md-5'],
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'rate:currency',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <div class="clearfix"></div>
	<div class="col-md-12">
        <?php echo Html::a('Create Program', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

</div>
