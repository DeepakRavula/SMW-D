<?php
use kartik\grid\GridView;
use common\models\InvoiceLineItem;
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
</style>
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
                    $invoiceLineItems = $timeVoucherDataProvider->query->all();
                    $costSum = 0.00;
                        foreach($invoiceLineItems as $invoiceLineItem) {
                            $costSum += $invoiceLineItem->cost;
                        }
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
                'group' => true,
            ],
            [
                'label' => 'Duration(hrs)',
                'value' => function ($data) {
                    return $data->unit;
                },
                'group' => true,
                'subGroupOf' => 0,
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
                'group' => true,
                'subGroupOf' => 0,
                'contentOptions' => ['class' => 'text-right'],
                'hAlign'=>'right',
                'pageSummary' => true,
                'pageSummary' => function ($summary, $data, $widget) use ($timeVoucherDataProvider) {
                    $invoiceLineItems = $timeVoucherDataProvider->query->all();
                    $costSum = 0.00;
                        foreach($invoiceLineItems as $invoiceLineItem) {
                            $costSum += $invoiceLineItem->cost;
                        }
                    return Yii::$app->formatter->asCurrency($costSum);
                },
                'visible' => Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')
            ]
        ];
    }
?>
<?= GridView::widget([
    'dataProvider' => $timeVoucherDataProvider,
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