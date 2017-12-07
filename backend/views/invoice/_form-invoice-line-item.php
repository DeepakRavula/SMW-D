<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="line-item-update" style="display:none;" class="alert-success alert fade in"></div>
<div id="invoice-line-item-modal" class="invoice-line-item-form">
    <div>
    <?php Pjax::Begin(['id' => 'item-add-listing', 'timeout' => 6000 ,'enablePushState' => false]); ?>
    <?= GridView::widget([
            'dataProvider' => $itemDataProvider,
            'summary' => false,
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
                'attribute' => 'price',
                'label' => 'Price',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return Yii::$app->formatter->asDecimal($data->price);
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
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000}); 
                    $.pjax.reload({container: "#invoice-view-tab-item", replace: false, async: false, timeout: 6000}); 
                }
            }
        });
        return false;
    });
    
    $(document).on('keyup paste, click', '#item-search, #item-picker-submit', function() {
        var searchVal = $(this).val().toLowerCase();
        $('#item-add-listing tbody > tr').addClass('hide');
        $('#item-add-listing tbody > tr').each(function(){
            var text = $(this).text().toLowerCase();
            if(text.indexOf(searchVal) != -1)
            {
                $(this).removeClass('hide');
            }
        });
        return false;
    });
</script>

