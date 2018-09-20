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
        //'rowOptions' => ['class' => 'choose-merge-customer'],
        'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
            $url = Url::to(['student/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
                if ($model->status === User::STATUS_NOT_ACTIVE) {
                    $data = array_merge($data, ['class' => 'danger inactive choose-merge-customer']);
                } elseif ($model->status === User::STATUS_ACTIVE) {
                    $data = array_merge($data, ['class' => 'info active choose-merge-customer']);
                }
            return $data;
        },
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
        var id = '<?= $model->id; ?>';
        var customerId = $(this).attr('data-key');
        $( ".choose-merge-customer" ).addClass("multiselect-disable");
        var params = $.param({ id: id, customerId: customerId });
                    $.ajax({
                        url    : '<?= Url::to(['customer/merge-preview']); ?>?'+params,
                        type   : 'get',
                        dataType: "json",
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-spinner').hide();
                                 $('#modal-content').html(response.data);
                                 $('#popup-modal').modal('show');                             
                            }
                            else {
                                $('#modal-spinner').hide();
                                $('#error-notification').html(response.errors).fadeIn().delay(8000).fadeOut();
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