<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = 'Items';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), ['#'], ['id' => 'create-item']);
$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="item-category-index">
    <?php Pjax::begin([
        'id' => 'item-listing',
        'timeout' => 6000,
    ]) ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'code',
            [
                'label' => 'Item Category',
        'value' => function ($data) {
            return $data->itemCategory->name;
        },
            ],
            'description',
            [
                'label' => 'Price',
                'contentOptions' => ['class' => 'text-right'],
                'headerOptions' => ['class' => 'text-right'],
                'format' => 'currency',
                'value' => function ($data) {
                    return !empty($data->price) ? $data->price : '0.00';
                },
            ],
            [
                'label' => 'Royalty Free',
        'value' => function ($data) {
            return $data->getRoyaltyFreeStatus();
        },
            ],
            [
                'label' => 'Tax',
        'value' => function ($data) {
            return $data->taxStatus->name;
        },
            ],
            [
                'label' => 'Status',
        'value' => function ($data) {
            return $data->getStatusType();
        },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

    <?php Modal::begin([
        'header' => '<h4 class="m-0">Item</h4>',
        'id' => 'item-edit-modal',
    ]); ?>
    <div id="item-edit-content"></div>
    <?php Modal::end(); ?>

<script>
    $(document).ready(function() {
        $(document).on('click', '#create-item, #item-listing  tbody > tr', function () {
            var itemId = $(this).data('key');
            if (itemId === undefined) {
                var customUrl = '<?= Url::to(['item/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['item/update']); ?>?id=' + itemId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#modal-content').html(response.data);
                    } else {
                        $('#error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#update-item-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#item-listing', timeout: 6000});
                        $('#item-edit-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.item-cancel', function () {
            $('#item-edit-modal').modal('hide');
            return false;
        });
        $("#itemsearch-showallitems").on("change", function() {
            var showAllItems = $(this).is(":checked");
            var url = "<?php echo Url::to(['item/index']); ?>?ItemSearch[showAllItems]=" + (showAllItems | 0);
            $.pjax.reload({url:url,container:"#item-listing",replace:false,  timeout: 4000});  //Reload GridView
        });
    });
</script>
