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
    <?php Pjax::Begin(['id' => 'item-add-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
            'dataProvider' => $itemDataProvider,
            'summary' => false,
            'id'=>'invoice-view-user-gridview',
            'tableOptions' => ['class' => 'table table-condensed'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'columns' => [
            [
                'label' => 'Code',
                'value' => function ($data) {
                    return $data->code;
                },
            ],
            [
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->description;
                },
            ],
            [
                'label' => 'Price',
                'value' => function ($data) {
                    return $data->price;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:50px'],
                'template' => '{add}',
                'buttons' => [
                    'add' => function ($url, $model) use($invoiceModel) {
                        $url = Url::to(['invoice/add-misc', 'id' => $invoiceModel->id, 'itemId' => $model->id]);
                        return Html::a('Add', null, ['class' => 'add-item-invoice', 'url' => $url]);
                    },
                ]
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
        $.ajax({
            url: $(this).attr('url'),
            type: 'get',
            success: function(response) {
                if (response.status) {
                    $('#invoice-line-item-modal').modal('hide');
                    $('#customer-update').html(response.message).fadeIn().delay(8000).fadeOut();
                    $.pjax.reload({container: "#invoice-bottom-summary", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-user-history", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#invoice-view-lineitem-listing", replace: false, async: false, timeout: 6000}); 
                    $.pjax.reload({container: "#invoice-view-tab-item", replace: false, async: false, timeout: 6000}); 
                }
            }
        });
        return false;
    });
    
    $(document).on('keyup paste', '#item-search', function() {
        var string = $(this).val();
        var params = $.param({ 'string': string });
        $.ajax({
            url: $(this).attr('url') + '&' + params,
            type: 'get',
            success: function(response) {
                if (response.status) {
                    $('#item-list-content').html(response.data);
                }
            }
        });
        return false;
    });
</script>
