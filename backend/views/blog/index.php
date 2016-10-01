<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogs';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i> Create', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
