<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>  

<?php $form = ActiveForm::begin([
    'action' => Url::to(['enrolment/group-confirm', 'GroupCourseForm[studentId]' => $model->studentId, 'GroupCourseForm[courseId]' => $model->courseId]),
    'id' => 'modal-form'
]); ?>

    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Payment Frequency Discount</label>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'pfDiscount')->textInput(['class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">%</label></div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Multiple Enrol. Discount (per month)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'enrolmentDiscount')->textInput(['class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
    </div>

<?php ActiveForm::end(); ?>


<script>
    $(document).ready(function () {
        $('#popup-modal').modal('show');
        $('.modal-save').show();
        $('#modal-back').hide();
        $('.modal-save').text('Confirm');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Discount Detail</h4>');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });
</script>