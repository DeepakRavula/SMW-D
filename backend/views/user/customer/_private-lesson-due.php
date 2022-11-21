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
        'title' => 'Private Lesson Due',
        'withBorder' => true,
    ])
    ?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'id' => 'customer-lesson-listing',
    'timeout' => 6000,
]) ?>
<?php $amount = 0;
    if (!empty($lessonDueDataProvider->getModels())) {
        foreach ($lessonDueDataProvider->getModels() as $key => $val) {
            $amount += $val->privateLesson->balance;
        }
    } ?>
<?php
echo GridView::widget([
    'dataProvider' => $lessonDueDataProvider,
    'options' => ['class' => 'col-md-12', 'id' => 'lesson-listing-customer-view'],
    'summary' => false,
    'emptyText' => false,
    'showFooter' => true,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);
        return ['data-url' => $url];
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Lesson Date',
            'value' => function ($data) {
                return $data->dueDate ? Yii::$app->formatter->asDate($data->date) : null;
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
                return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
            },
        ],
        [
            'label' => 'Amount',
            'attribute' => 'owing',
            'contentOptions' => ['class' => 'text-right dollar'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal(round($data->privateLesson->balance ?? 0, 2));
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => function($data) use($model) {
                return  Yii::$app->formatter->asCurrency(round($model->getPrivateLessonsDue($model->id), 2));
            },
            'footer' => Yii::$app->formatter->asCurrency(round($amount ?? 0, 2)),
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
<?php LteBox::end() ?>