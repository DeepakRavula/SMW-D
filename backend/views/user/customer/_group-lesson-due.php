<?php
use common\models\Lesson;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Enrolment;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;
use common\models\Invoice;
?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Group Lesson Due',
        'withBorder' => true,
    ])
    ?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'id' => 'customer-group-lesson-listing',
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $lessonDueDataProvider,
    'options' => ['class' => 'col-md-12', 'id' => 'lesson-listing-customer-view'],
    'summary' => false,
    'emptyText' => false,
    'showPageSummary' => true,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->lessonId]);
        return ['data-url' => $url];
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Lesson Date',
            'value' => function ($data) {
                return $data->lesson->date ? Yii::$app->formatter->asDate($data->lesson->date) : null;
            },
        ],
        [
            'label' => 'Student',
            'value' => function ($data) {
                return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
            },
        ],
        [
            'label' => 'Program',
            'value' => function ($data) {
                return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
            },
        ],
        [
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->lesson->teacher->publicIdentity;
            },
        ],
        [
            'label' => 'Amount',
            'attribute' => 'owing',
            'contentOptions' => ['class' => 'text-right dollar'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal(round($data->total, 2));
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => function($data) use($model) {
                return  Yii::$app->formatter->asCurrency(round($model->getGroupLessonOwingAmount($model->id), 2));
            }
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
<?php LteBox::end() ?>