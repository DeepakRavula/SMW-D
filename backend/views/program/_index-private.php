<?php

use yii\widgets\Pjax;
use yii\bootstrap\ButtonGroup;
use common\models\Program;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use yii\helpers\Html;
use backend\models\search\ProgramSearch;

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
    <div class="m-t-10">
    <?php Pjax::begin(['id' => 'program-listing', 'enablePushState' => false]) ?>
    <?php 
        $columns = [
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'width:250px;'],
                'value' => function ($data) {
                    return $data->name;
                },
            ],
        ];
        if ($searchModel->type == ProgramSearch::PROGRAM_TYPE_PRIVATE) {
            $label = 'Rate Per Hour';
        } else {
            $label = 'Rate Per Course';
        }
            array_push($columns, [
                'label' => $label,
                'attribute' => 'rate',
                'format' => 'currency',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'style' => 'width:100px;'],
                'value' => function ($data) {
                    return !empty($data->rate) ? $data->rate : null;
                },
            ]);
    ?>
    <?php
    echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model) use ($searchModel) {
                if ($searchModel->showAllPrograms) {
                    if ((int)$model->status === Program::STATUS_INACTIVE) {
                        return ['class' => 'danger inactive'];
                    } elseif ((int)$model->status === Program::STATUS_ACTIVE) {
                        return ['class' => 'info active'];
                    }
                }
            },
        'columns' => $columns,
        'toolbar' =>  [
            [
                'content' =>
                    Html::a('<i class="fa fa-plus"></i>', '#', [
                        'class' => 'btn btn-success new-program'
                    ]),
                'options' => ['title' =>'Add',
                              'class' => 'btn-group mr-2']
                ],
            ['content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            'options' => ['title' =>'Filter',]
               ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Programs'
        ],
    ]);
    ?>
<?php Pjax::end(); ?>
</div>
</div>
</div>
