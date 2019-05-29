<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\search\UserSearch;
use yii\helpers\ArrayHelper;
use common\models\Student;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Prepaid Lessons',
        'withBorder' => true,
    ])
    ?>

<div class="clearfix"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
    'id' => 'customer-prepaid-lessons-grid'
]) ?>
<?php echo  GridView::widget([
    'dataProvider' => $prePaidLessonsDataProvider,
    'options' => ['class' => 'col-md-12', 'id' => 'account-receivable-report-prepaid-lessons'],
    'summary' => false,
    'emptyText' => false,
    'showPageSummary' => true,
    'tableOptions' => ['class' => 'table table-bordered table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'columns' => [
        [
            'label' => 'ID',
            'value' => function ($data) {
                return $data->getLessonNumber();
            },
        ],
        [
            'label' => 'Lesson Date',
            'value' => function ($data) {
                return $data->dueDate ? Yii::$app->formatter->asDate($data->date) : null;
            },
        ],
        [
            'label' => 'Paid',
            'attribute' => 'owing',
            'contentOptions' => ['class' => 'text-right dollar total-prepaid-lessons'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                $lessonPaid = !empty($data->getCreditAppliedAmount($data->enrolment->id)) ? $data->getCreditAppliedAmount($data->enrolment->id) : 0;
                return round($lessonPaid, 2);
            },
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM,
        ],
       
    ],
]); ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<?php LteBox::end() ?>
	
