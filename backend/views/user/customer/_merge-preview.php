<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'tableOptions' => ['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'options' => ['class' => 'col-md-10'],
'summary' => false,
'emptyText' => false,
'columns' => [
    [
        'label' => 'Student',
        'value' => function ($data)  {
            return !empty($data->fullName) ? $data->fullName : null;
        },
    ],
    [
        'label' => 'Customer',
        'value' => function ($data)  {
            return !empty($data->customer) ? $data->customer->publicIdentity : null;
        },
    ],
    [
        'label' => 'Action',
        'value' => function ($data) use ($model) {
            return 'Will be merged to '.$model->publicIdentity;
        },
    ],
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
<div class="clearfix"></div>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Customer Merge Preview</h4>');
        $('#popup-modal .modal-dialog').css({'width': '800px'});
        $('#modal-popup-warning-notification').html('Merging another customer will delete all of their contact data. This can not be undone.').fadeIn();
        $('#modal-apply').text('Confirm');
        $('#modal-apply').show();  
    });

     $(document).off('click', '#modal-apply').on('click', '#modal-apply', function() {
        $('#modal-spinner').show();
        var customerId = '<?= $mergeUserModel->id; ?>';
        var params = $.param({customerId: customerId });
                    $.ajax({
                        url    : '<?= Url::to(['customer/merge', 'id' => $model->id]); ?>&'+params,
                        type   : 'post',
                        dataType: "json",
                        data   : $(this).serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-spinner').hide();
                                $('#popup-modal').modal('hide');  
                                bootbox.alert(response.message);                   
                            }
                            else {
                                $('#modal-spinner').hide();
                                $('#error-notification').html(response.errors).fadeIn().delay(8000).fadeOut();
                            }
                        }
                    });
        return false;
    });

</script>