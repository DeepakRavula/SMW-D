<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;

?>
<div class="payments-form">
    <div id="loader" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div id="vacation-conflict" style="display: none;" class="alert-danger alert fade in"></div>
    <?php
    $form = ActiveForm::begin([
            'id' => 'vacation-create-form',
            'action' => Url::to(['vacation/create', 'enrolmentId' => $enrolmentId])
    ]);
    ?>
    <div class="row">
        <div class="form-group">
            <div class="col-md-6">
                <?php
                echo DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'pluginOptions' => [
                        'autoApply' => true,
                        'locale' => [
                            'format' => 'M d,Y',
                        ],
                        'opens' => 'bottom',
                    ],
                ]);
                ?>
            </div>
            <div class="row-fluid">
                <div class="form-group">
<?php echo Html::submitButton(
                    Yii::t(
                    'backend',
        'Confirm & Create'
                ),
    ['class' => 'btn btn-info', 'name' => 'signup-button']
                ) ?>
            <?= Html::a(
        'Cancel',
        '#',
                ['class' => 'btn btn-default vacation-cancel-button']
    ); ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
            <?php
            yii\widgets\Pjax::begin([
                'id' => 'review-listing',
                'timeout' => 6000,
            ])
            ?>

            <?php if ($lessonDataProvider) : ?>
                <?php
                $columns         = [
                    [
                        'label' => 'Teacher',
                        'value' => function ($model) {
                            return $model->teacher->publicIdentity;
                        },
                        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                        'contentOptions' => ['class' => 'kv-sticky-column'],
                    ],
                    [
                        'label' => 'Program',
                        'value' => function ($model) {
                            return $model->course->program->name;
                        },
                        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                        'contentOptions' => ['class' => 'kv-sticky-column'],
                    ],
                    [
                        'label' => 'Date/Time',
                        'attribute' => 'date',
                        'format' => 'datetime',
                        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                        'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:150px;'],
                    ],
                    [
                        'attribute' => 'duration',
                        'value' => function ($model) {
                            return (new \DateTime($model->duration))->format('H:i');
                        },
                        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                        'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:80px;'],
                    ]
                ];
                ?>
                <div class="col-lg-12">
                    <?= '<label class="control-label">Affected Lessons</label>' ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $lessonDataProvider,
                        'columns' => $columns,
                        'summary' => false,
                        'emptyText' => false,
                    ]);
                    ?>
                </div>
                <?php endif; ?>
                <?php if ($paymentCycleDataProvider) : ?>
                <div class="col-lg-12">
                    <?php
                    $paymentCyclecolumns = [
                        [
                            'label' => 'Start Date',
                            'value' => function ($model) {
                                return $model->startDate;
                            },
                            'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                            'contentOptions' => ['class' => 'kv-sticky-column'],
                        ],
                        [
                            'label' => 'End Date',
                            'value' => function ($model) {
                                return $model->endDate;
                            },
                            'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
                            'contentOptions' => ['class' => 'kv-sticky-column'],
                        ]
                    ];
                    ?>
                <?= '<label class="control-label">Affected Payment Cycles</label>' ?>
    <?=
    GridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'columns' => $paymentCyclecolumns,
        'summary' => false,
        'emptyText' => false,
    ]);
    ?>
                </div>
<?php endif; ?>
<?php if ($creditAmount) : ?>
                <div class="col-lg-12">
    <?= '<label class="control-label">Estimated credits to be transferred to customer\'s account: </label>' ?> <?= $creditAmount; ?>
                </div>
<?php endif; ?>
<?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>
</div>

<script>
    $(document).on('beforeSubmit', '#vacation-create-form', function () {
        $('#loader').show();
        $.ajax({
            url: "<?= Url::to(['vacation/create', 'enrolmentId' => $enrolmentId]); ?>",
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                $('#loader').hide();
                if (response.status) {
                    $('#vacation-modal').modal('hide');
                    $('#enrolment-delete-success').html('Vacation has been created successfully').fadeIn().delay(3000).fadeOut();
                    $.pjax.reload({container: "#enrolment-vacation", replace: false, async: false, timeout: 4000});
                    $.pjax.reload({container: "#payment-cycle-listing", replace: false, async: false, timeout: 4000});
                    $.pjax.reload({container: "#lesson-index", replace: false, async: false, timeout: 4000});
                } else{
                    $('#vacation-conflict').html(response.errors).fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });
$(document).off('change', '#vacation-daterange').on('change', '#vacation-daterange', function () {		 
         var dateRange = $(this).val();
         var url = "<?= Url::to(['vacation/create', 'enrolmentId' => $enrolmentId]); ?>&dateRange=" + dateRange;
         $('#loader').show();
         $.pjax.reload({url: url, container: "#review-listing", replace: false, async: false, timeout: 4000});
         $('#loader').hide();
         return false;
     });

</script>
