<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$this->params['action-button'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-row-open p-10">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::toRoute(['city/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'columns' => [
			[
                'attribute' => 'name',
				'label' => 'Name',
				'value' => function($data) {
					return ! empty($data->name) ? $data->name : null;
				}
			],
			[
                'attribute' => 'province_id',
				'label' => 'Province Name',
				'value' => function($data) {
					return ! empty($data->province->name) ? $data->province->name :null;
				}
			],
        ],
    ]); ?>

</div>
