<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\ActiveForm;
use common\models\Program;
use yii\grid\GridView;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles    = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>
 <div class="box">
    <div class="box-body">
<?= ButtonGroup::widget([
	'buttons' => [
		Html::a('Private', '', ['class' => ['btn btn-default active', 'private'],
			'value' => 1]),
		Html::a('Group', '', ['class' => ['btn btn-default', 'group'],
			'value' => 2]),
	]
]); ?>
    <div>
    <?php Pjax::begin(['id' => 'program-listing', 'enablePushState' => false]) ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        
        'columns' => [
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'width:250px;'],
                'value' => function ($data) {
                    return $data->name;
                },
            ],
            [
                'label' => 'Rate Per Hour',
                'attribute' => 'rate',
                'format' => 'currency',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'style' => 'width:100px;'],
                'value' => function ($data) {
                    return !empty($data->rate) ? $data->rate : null;
                },
            ],
        ],
    ]);
    ?>
<?php Pjax::end(); ?>
</div>
</div>
</div>
