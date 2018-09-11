<?php

use common\models\City;
use common\models\Country;
use common\models\Province;
use yii\helpers\ArrayHelper;
use common\models\Label;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use common\models\ReferralSource;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */

?>
<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['reverse-enrolment/add-customer', 'EnrolmentForm' => $courseDetail])
    ]);
?>
<div class="user-create-form">
    <div class="row">
        <div class="col-xs-3">
            <label class="modal-form-label">Customer Name</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, 'firstname')->textInput(['placeholder' => 'First Name'])->label(false); ?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($courseDetail, 'lastname')->textInput(['placeholder' => 'Last Name'])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <label class="modal-form-label">Customer Email</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, "email")->textInput(['placeholder' => 'Email', 'maxlength' => true])->label(false) ?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($courseDetail, "labelId")->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Label::find()
                        ->andWhere(['userAdded' => false])
                        ->all(), 'id', 'name'),
                'pluginOptions' => [
                    'tags' => true,
                ],
            ])->label(false);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <label class="modal-form-label">Customer Phone</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, 'number')->widget(MaskedInput::className(), [
                'mask' => '(999) 999-9999',
                'options' => [
                    'placeholder' => 'number',
                    'class' => 'form-control'
                ]
               ])->label(false);
            ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($courseDetail, 'extension')->textInput(['placeholder' => 'Ext'])->label(false); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($courseDetail, "phoneLabelId")->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Label::find()
                        ->andWhere(['userAdded' => false])
                        ->all(), 'id', 'name'),
                'options' => [
                    'id' => 'phone-label',
                ],
                'pluginOptions' => [
                    'tags' => true,
                ],
                ])->label(false);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <label class="modal-form-label">Customer Address</label>
        </div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, "addressLabelId")->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Label::find()
                        ->andWhere(['userAdded' => false])
                        ->all(), 'id', 'name'),
                'options' => [
                    'id' => 'address-label',
                ],
                'pluginOptions' => [
                    'tags' => true,
                ],
            ])->label(false);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-9">
            <?= $form->field($courseDetail, 'address')->textInput(['placeholder' => 'Street Address'])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, 'cityId')->dropDownList(
                ArrayHelper::map(City::find()->notDeleted()->all(), 'id', 'name')
            )->label(false);
            ?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($courseDetail, 'provinceId')->dropDownList(
                ArrayHelper::map(Province::find()->all(), 'id', 'name'))->label(false);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-4">
            <?= $form->field($courseDetail, 'countryId')->dropDownList(
                ArrayHelper::map(Country::find()->all(), 'id', 'name'))->label(false);
            ?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($courseDetail, 'postalCode')->textInput(['placeholder' => 'Postal Code'])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <label class="modal-form-label">How did you find us?</label>
        </div>
        <div class="col-xs-9">
        <div id = "referal-source">
            <?php 
            $referralSource = ReferralSource::find()
            ->notDeleted()
            ->all();
            $referralSourceList = ArrayHelper::map($referralSource, 'id', 'name');
            echo  $form->field($courseDetail, 'referralSourceId')->radioList($referralSourceList)->label(false);
            echo  $form->field($courseDetail, 'description')->textInput()->label(false);
            ?>
        </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Customer Details</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Next');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('#modal-spinner').hide();
        $('#modal-back').removeClass();
        $('#modal-back').addClass('btn btn-info add-customer-back');
        $("#enrolmentform-description").hide();
    });

    $(document).off('click', '.add-customer-back').on('click', '.add-customer-back', function () {
        $('#modal-spinner').show();
        $.ajax({
            url: '<?= Url::to(['course/create-enrolment-detail', 'studentId' => !empty($student) ? $student->id : null,
                'isReverse' => true, 'EnrolmentForm' => $courseDetail]) ?>',
            type: 'get',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                if (response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#modal-spinner').hide();
                }
            }
        });
        return false;
    });
    $(document).off('click', 'input:radio[name="EnrolmentForm[referralSourceId]"]').on('click', 'input:radio[name="EnrolmentForm[referralSourceId]"]', function () {
        var referralSourceId = $('input:radio[name="EnrolmentForm[referralSourceId]"]:checked').val();
        if(referralSourceId == '4') {
            $("#enrolmentform-description").show();
        }  else {
            $("#enrolmentform-description").hide();
        }
    });   
</script>