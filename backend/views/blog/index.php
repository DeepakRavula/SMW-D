<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogs';
$this->params['action-button'] = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Create', ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-row-open p-20">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['blog/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'columns' => [
			[
				'label' => 'User Name',
				'value' => function($data){
					return $data->user->publicIdentity;
				}
			],
			[
				'label' => 'Title',
				'format' => 'raw',
				'value' => function($data){
					return substr($data->title,0,25) . ' ...';
				}
			],
			[
				'label' => 'Content',
				'format' => 'raw',
				'value' => function($data){
					return substr($data->content,0,25) . ' ...';
				}
			],
            'date:date',
        ],
    ]); ?>

</div>
