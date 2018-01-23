<?php

use yii\data\ActiveDataProvider;
use common\models\Vacation;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$vacations = Vacation::find()
    ->joinWith(['enrolment' => function ($query) use ($studentModel) {
        $query->andWhere(['studentId' => $studentModel->id]);
    }])
    ->andWhere(['vacation.isConfirmed' => true, 'vacation.isDeleted' => false]);
$vacationDataProvider = new ActiveDataProvider([
    'query' => $vacations,
    ]);
?>
<div>
    <?php
    yii\widgets\Pjax::begin([
        'timeout' => 6000,
        'id' => 'student-vacation',
    ])
    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $vacationDataProvider,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12', 'id' => 'student-vacation-list',],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'emptyText'=>'This student has no vacations to list. Vacations must be entered for each enrolment. To add a vacation for this student, go into their enrolment and add one there.',
        'columns' => [
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return $data->enrolment->course->program->name;
                },
            ],
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
        ],
    ]);
    ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>