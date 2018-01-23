<?php

use yii\data\ActiveDataProvider;
use common\models\Vacation;
use yii\grid\GridView;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$vacations = Vacation::find()
    ->andWhere([
        'enrolmentId' => $model->id,
        'vacation.isConfirmed' => true,
        'vacation.isDeleted' => false
    ]);
$vacationDataProvider = new ActiveDataProvider([
    'query' => $vacations,
    ]);
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Vacations',
    'withBorder' => true,
])
?>
<div>
    <?php
    echo GridView::widget([
        'dataProvider' => $vacationDataProvider,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12', 'id' => 'student-vacation-list',],
        'tableOptions' => ['class' => 'table table-condensed'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'From Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->fromDate);
                },
            ],
            [
                'label' => 'To Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->toDate);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a(
                            '<i class="fa fa-trash-o"></i>',
                            ['#'],
                                [
                                'id' => 'vacation-delete-'.$model->id,
                                'title' => Yii::t('yii', 'Delete'),
                                'class' => 'vacation-delete m-l-10 btn-danger btn-xs',
                        ]
                        );
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
<?php LteBox::end()?>
