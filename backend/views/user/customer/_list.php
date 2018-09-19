<?php

use common\components\gridView\KartikGridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

?>
<div id = "success-notification" style = "display: none;" class = "alert-danger alert fade in"></div>
<div id = "error-notification" style = "display: none;" class = "alert-danger alert fade in"></div>
<?php Pjax::Begin(['id' => 'merge-customer-add-listing', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'options' => ['id' => 'merge-choose-customer'],
        'dataProvider' => $customerDataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => ['class' => 'choose-merge-customer'],
        'tableOptions' => ['class' => 'table table-condensed'],
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['customer/merge', 'id' => $model->id, "UserSearch[role_name]" => User::ROLE_CUSTOMER, 
            "UserSearch[showAll]" => true]),
        'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                }
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                }
            ],
            'email',
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->user->phoneNumber->number) ? $data->user->phoneNumber->number : null;
                },
            ]
        ]
    ]); ?>
<?php Pjax::end(); ?>

<?php $form = ActiveForm::begin([
    'id' => 'modal-form'
]); ?>

    <?= $form->field($model, 'customerId')->hiddenInput()->label(false); ?>
    
<?php ActiveForm::end(); ?>

<script>
    $(document).off('click', '.choose-merge-customer').on('click', '.choose-merge-customer', function() {
        $('#modal-spinner').show();
        var customerId = $(this).attr('data-key');
        $('#user-customerid').val(customerId);
        bootbox.confirm({
            message: "Are you sure you want to merge? <hr> <div class = 'apply-credit-error-alert'> Operations Performed cannot be revoked !!! </div>",
            callback: function (result) {
                if (result) {
                    $('.bootbox').modal('hide');
                    $( ".choose-merge-customer" ).addClass("multiselect-disable");
                    $.ajax({
                        url    : '<?= Url::to(['customer/merge' ,'id' => $model->id]); ?>',
                        type   : 'post',
                        dataType: "json",
                        data   : $('#modal-form').serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-spinner').hide();
                                $('#success-notification').html(response.message).fadeIn().delay(8000).fadeOut();
                                $.pjax.reload({container: "#customer-student-listing", replace: false, async: false, timeout: 6000});
                                $.pjax.reload({container: "#customer-enrolment-listing", replace: false, async: false, timeout: 6000});
                                $.pjax.reload({container: "#customer-lesson-listing", replace: false, async: false, timeout: 6000});
                                $.pjax.reload({container: "#user-log", replace: false, async: false, timeout: 6000}); 
                                $('#popup-modal').modal('hide');

                            }
                            else {
                                $('#modal-spinner').hide();
                                $('#error-notification').html(response.errors).fadeIn().delay(8000).fadeOut();
                            }
                        }
                    });
                } else {
                    $('#modal-spinner').hide();
                    $('#popup-modal').modal('hide');
                }
            }
                });
        return false;
    });

    $(document).ready(function(){
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Customer Merge</h4>');
		$('#popup-modal .modal-dialog').css({'width': '800px'});
        $('.modal-save').hide();
    });
</script>