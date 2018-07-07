<?php

use yii\data\ActiveDataProvider;
use common\models\Vacation;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
<div class="vacation-index">
    <?php yii\widgets\Pjax::begin([
        'id' => 'vacation-index',
        'timeout' => 6000,
    ]) ?>
<?php echo GridView::widget([
    'dataProvider' => $vacationDataProvider,
    'summary' => false,
     'emptyText'=>'This student has no vacations to list. Vacations must be entered for each enrolment. To add a vacation for this student, go into their enrolment and add one there.',
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'options' =>['id' => 'student-vacation-list'],
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
]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
