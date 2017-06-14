<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-index">


    <p>
        <?php echo Html::a('Create Item', ['#'], ['class' => 'btn btn-success', 'id' => 'create-item']) ?>
    </p>
    <?php Pjax::begin([
        'id' => 'item-listing',
        'timeout' => 6000,
    ]) ?>
    <?php echo GridView::widget([
        'id' => 'item-grid',
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
        $('#create-item, #item-listing  tbody > tr').on('click', function() {
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
                    } else {
                        $('#update-item-form').yiiActiveForm('updateMessages', response.errors, true);
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.item-cancel', function () {
            $('#item-edit-modal').modal('hide');
            return false;
        });
    });

</script>
