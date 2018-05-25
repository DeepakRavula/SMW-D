<?php

use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
?>

<div id="bulk-reschedule" style="display: none;" class="alert-danger alert fade in"></div>
<div class="enrolment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['enrolment/update', 'id' => $model->id])
    ]); ?>
    <div class="user-create-form">
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($courseReschedule, 'dateRangeToChangeSchedule')->widget(DateRangePicker::classname(), [
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'pluginOptions' => [
                        'autoApply' => true,
                        'ranges' => [
                            Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => [
                                "moment().startOf('day').subtract(6, 'days')", 'moment()'
                            ],
                            Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => [
                                "moment().startOf('day').subtract(29, 'days')", 'moment()'
                            ],
                            Yii::t('kvdrp', 'This Month') => [
                                "moment().startOf('month')", "moment().endOf('month')"
                            ],
                            Yii::t('kvdrp', 'Last Month') => [
                                "moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"
                            ]
                        ],
                        'locale' => [
                            'format' => 'M d, Y'
                        ],
                        'opens' => 'right'
                    ]
                ])->label('DateRange To Change Schedule'); ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($courseReschedule, 'rescheduleBeginDate')->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                ],
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'defaultDate' => (new \DateTime($courseReschedule->rescheduleBeginDate))->format('M d, Y'),
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ]
            ])->label('Reschedule Begin Date') ?>
            </div>
        </div>
    </div>
    <?= $form->field($courseReschedule, 'teacherId')->hiddenInput()->label(false);?>
    <?= $form->field($courseReschedule, 'duration')->hiddenInput()->label(false);?>
    <?= $form->field($courseReschedule, 'dayTime')->hiddenInput()->label(false);?>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('modal-next', function(event, params) {
        $('.modal-save').text('Preview lessons');
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
    });
</script>