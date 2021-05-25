<?php
use kartik\grid\GridView;
use common\models\InvoiceLineItem;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<style>
td.kv-group-even {
    background-color: white!important;
}
td.kv-group-odd {
    background-color: white!important;
}
.table > tbody > tr > td, .table > tfoot > tr > td {
        padding: 0px;  
}
</style>
<?php 
    $teacherId = $model->id;
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
                    $costSum = 0.00;
                    $invoiceLineItems = InvoiceLineItem::find()
                        ->notDeleted()
                        ->joinWith(['invoice' => function ($query) use ($fromDate, $toDate) {
                            $query->notDeleted()
                                ->invoice()
                                ->between((new \DateTime($fromDate))->format('Y-m-d'), (new \DateTime($toDate))->format('Y-m-d'));
                        }])
                        ->joinWith(['lesson' => function ($query) use ($teacherId) {
                            $query->andWhere(['lesson.teacherId' => $teacherId])
                            ->groupBy('lesson.id');
                        }])
                        ->all();
                        foreach($invoiceLineItems as $invoiceLineItem) {
                           
                            $costSum += $invoiceLineItem->cost;
                        }
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
            ],
            [
                'label' => 'Cost',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency($data->cost);
                },
                'contentOptions' => ['class' => 'text-right'],
                'hAlign' => 'right',
                'pageSummary' => function ($summary, $data, $widget) use ($invoicedLessonsDataProvider) {
                    $invoiceLineItems = $invoicedLessonsDataProvider->query->all();
                    $costSum = 0.00;
                        foreach($invoiceLineItems as $invoiceLineItem) {
                            $costSum += $invoiceLineItem->cost;
                        }
                    return Yii::$app->formatter->asCurrency($costSum);
                },
            ],
        ];
?>
<?php LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Invoiced Lessons',
    'withBorder' => true,
]) ?>
<div class="col-md-12">
    <?php $form = ActiveForm::begin([
        'id' => 'time-voucher-search-form',
    ]); ?>
    
    <div class="row">
        <div class="col-md-3 form-group">
            <?= DateRangePicker::widget([
                'model' => $invoiceSearchModel,
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
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
<?= GridView::widget([
    'dataProvider' => $invoicedLessonsDataProvider,
    'summary' => false,
    'emptyText' => false,
    'options' => ['class' => 'col-md-12', 'id' => 'teacher-lesson-cost-grid'],
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
<?php LteBox::end() ?>

<script>
    $(document).on('beforeSubmit', '#time-voucher-search-form', function () {
        var dateRange = $('#invoicesearch-daterange').val();
        var params = $.param({ 'InvoiceSearch[dateRange]': dateRange});
        $.pjax.reload({container: "#time-voucher-grid", replace: false, timeout: 6000, data: $(this).serialize()});
        return false;
    });

    $(document).off('change', '#invoicesearch-daterange').on('change', '#invoicesearch-daterange', function() {
        debugger;
        var invoiceDateRange = $('#invoicesearch-daterange').val();
        var result = invoiceDateRange.split('-');
        var fromDate = result[0].trim();
        var toDate = result[1].trim();
        var checkFromDate =  moment(fromDate).format('YY-MM-DD');
        var checkToDate = moment(toDate).format('YY-MM-DD');
        if (checkFromDate === 'Invalid date' || checkToDate === 'Invalid date') {
            $('#invoicesearch-daterange').parent().append('<p class="help-block help-block-error"><div style="color:#dd4b39">Invalid Format</div></p>');
            $('#invoicesearch-daterange').val("");
        } else {
            $("#time-voucher-search-form").submit();
        }
        return false;
    });
</script>