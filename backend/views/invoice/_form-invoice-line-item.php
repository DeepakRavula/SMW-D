<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="invoice-line-item-modal" class="invoice-line-item-form">
    <div>
    <?php Pjax::Begin(['id' => 'item-add-listing', 'timeout' => 6000 ,'enablePushState' => false]); ?>
    <?= GridView::widget([
            'dataProvider' => $itemDataProvider,
            'summary' => false,
            'emptyText' => false,
            'filterModel' => $itemSearchModel,
            'tableOptions' => ['class' => 'table table-condensed'],
            'rowOptions' => ['class' => 'add-item-invoice'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
            [
                'attribute' => 'code',
                'label' => 'Code',
                'value' => function ($data) {
                    return $data->code;
                },
            ],
            [
                'attribute' => 'description',
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
            ],
            [
                'label' => 'Price',
                'format' => 'currency',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return round($data->price, 2);
                },
            ],
                  
        ],
    ]); ?>
    </div>
    <?php Pjax::end(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::a('Cancel', '', ['class' => 'btn btn-default add-misc-cancel']);?>    
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '.add-item-invoice', function() {
        $('#item-spinner').show();
        var itemId=$(this).attr('data-key');
             var params = $.param({'itemId': itemId });
        $.ajax({
            url    : '<?= Url::to(['invoice/add-misc' ,'id' => $invoiceModel->id]); ?>&' + params,
            type: 'post',
            success: function(response) {
                if (response.status) {
                    $('#item-spinner').hide();
                    $('#line-item-update').html(response.message).fadeIn().delay(8000).fadeOut();
                    $.pjax.reload({container: "#invoice-header-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000}); 
                    $.pjax.reload({container: "#invoice-view-tab-item", replace: false, async: false, timeout: 6000});
					$('#invoice-line-item-modal').modal('hide');
                }
            }
        });
        return false;
    });
</script>

