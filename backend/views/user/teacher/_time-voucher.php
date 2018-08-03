<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\InvoiceLineItem;

?>
<div class="col-md-12">
    <?php $form = ActiveForm::begin([
        'id' => 'time-voucher-search-form',
    ]); ?>
    
    <div class="row">
        <div class="col-md-3 form-group">
            <?= DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]); ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::a('<i class="fa fa-print"></i> Print', ['print/time-voucher', 'id' => $model->id], ['id' => 'time-voucher-print-btn', 'class' => 'btn btn-default m-r-10', 'target' => '_blank']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<?php 
    $teacherId = $model->id;
    if (!$searchModel->summariseReport) {
        $columns = [
            [
                'value' => function ($data) {
                    $invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                    return $invoiceDate->format('l, F jS, Y');
                },
                'group' => true,
                'groupedRow' => true,
                'groupFooter' => function ($model, $key, $index, $widget) use ($teacherId) {
                    $fromDate = $model->invoice->date;
                    $toDate = $model->invoice->date;
                    $costSum = InvoiceLineItem::find()
                        ->notDeleted()
                        ->joinWith(['invoice' => function ($query) use ($fromDate, $toDate) {
                            $query->notDeleted()
                                ->invoice()
                                ->between((new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d'));
                        }])
                        ->joinWith(['lesson' => function ($query) use ($teacherId) {
                            $query->andWhere(['lesson.teacherId' => $teacherId]);
                        }])
                        ->sum('cost');
                    return [
                        'mergeColumns' => [[1, 3]],
                        'content' => [
                            4 => GridView::F_SUM,
                            6 => Yii::$app->formatter->asCurrency(round($costSum, 2)),
                        ],
                        'contentFormats' => [
                            4 => ['format' => 'number', 'decimals' => 2]
                        ],
                        'contentOptions' => [
                            4 => ['style' => 'text-align:right'],
                            6 => ['style' => 'text-align:right'],
                        ],
                    'options'=>['style'=>'font-weight:bold;']
                    ];
                }
            ],
            [
                'label' => 'Time',
                'width' => '250px',
                'value' => function ($data) {
                    return !empty($data->lesson->date) ? Yii::$app->formatter->asTime($data->lesson->date) : null;
                }
            ],
            [
                'label' => 'Program',
                'width' => '250px',
                'value' => function ($data) {
                    return  !empty($data->lesson->enrolment->program->name) ? $data->lesson->enrolment->program->name : null;
                }
            ],
            [
                'label' => 'Student',
                'value' => function ($data) {
                    $student = ' - ';
                    if ($data->lesson->course->program->isPrivate()) {
                        $student = !empty($data->lesson->enrolment->student->fullName) ? $data->lesson->enrolment->student->fullName : null;
                    }
                    return $student;
                }
            ],
            [
                'label' => 'Duration(hrs)',
                'value' => function ($data) {
                    return $data->unit;
                },
                'contentOptions' => ['class' => 'text-right'],
                    'hAlign' => 'right',
                    'pageSummary' => true,
                    'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Rate/hr',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency(round($data->rate, 2));
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'visible' => Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')
            ],
            [
                'label' => 'Cost',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->cost);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => function ($summary, $data, $widget) use ($timeVoucherDataProvider) {
                    $costSum = $timeVoucherDataProvider->query->sum('cost');
                    return Yii::$app->formatter->asCurrency($costSum);
                },
                'visible' => Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')
            ],
        ];
    } else {
        $columns = [
            [
                'label' => 'Date',
                'value' => function ($data) {
                    $invoiceDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
                    return $invoiceDate->format('l, F jS, Y');
                },
            ],
            [
                'label' => 'Duration(hrs)',
                'value' => function ($data) {
                    return $data->getLessonDuration($data->invoice->date, $data->lesson->teacherId);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            [
                'label' => 'Cost',
                'value' => function ($data) {
                    return $data->getLessonCost($data->invoice->date, $data->lesson->teacherId);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign'=>'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM,
                'visible' => Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')
            ]
        ];
    }
?>
<?= GridView::widget([
    'dataProvider' => $timeVoucherDataProvider,
    'summary' => false,
    'emptyText' => false,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'pjax' => true,
    'showPageSummary' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'time-voucher-grid'
        ]
    ],
    'columns' => $columns
]); ?>

<script>
    $(document).on('beforeSubmit', '#time-voucher-search-form', function () {debugger
        var dateRange = $('#invoicesearch-daterange').val();
        var params = $.param({ 'InvoiceSearch[dateRange]': dateRange});
        $.pjax.reload({container: "#time-voucher-grid", replace: false, timeout: 6000, data: $(this).serialize()});
        var url = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
        $('#time-voucher-print-btn').attr('href', url);
        return false;
    });
</script>