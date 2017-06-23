<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;
Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-index">
    <style>
  .e1Div{
    right: 0 !important;
    top: -59px;
  }
</style>
<div class="student-index">
    <div class="pull-right  m-r-20">
    <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]); ?>
    </div>
    <?php yii\widgets\Pjax::begin() ?>
    <div class="schedule-index">
        <div class="e1Div">
            <?= $form->field($searchModel, 'showAllItems')->checkbox(['data-pjax' => true])->label('Show All'); ?>
        </div>
    </div>

    <?php \yii\widgets\Pjax::end(); ?>
    <?php ActiveForm::end(); ?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
    <p>
        <?php echo Html::a('Create Item', ['#'], ['class' => 'btn btn-success', 'id' => 'create-item']) ?>
    </p>
    <?php Pjax::begin([
        'id' => 'item-listing',
        'timeout' => 6000,
    ]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'code',
            [
                'label' => 'Item Category',
		'value' => function ($data) {
                    return $data->itemCategory->name;
                },
            ],
            'description',
            'price',
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
                        $('#item-edit-content').html(response.data);
                        $('#item-edit-modal').modal('show');
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
