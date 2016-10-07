<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provinces';
$this->params['action-button'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-row-open">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['province/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        //'filterModel' => $searchModel,
        'columns' => [
            'name',
            'tax_rate',
			[
				'label' => 'Country Name',
				'value' => function($data){
					return ! empty($data->country->name) ? $data->country->name : null; 
				}
			],
        ],
    ]); ?>

</div>
