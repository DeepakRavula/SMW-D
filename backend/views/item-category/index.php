<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Categories';

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), [''], ['class' => 'btn btn-primary btn-sm', 'id' => 'create-item-category']);
$this->params['action-button'] = $addButton;
?>
<div class="item-category-index">

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php Pjax::begin([
    'id' => 'item-category-listing',
    'timeout' => 6000,
]) ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>

    <?php Modal::begin([
        'header' => '<h4 class="m-0">Item Category</h4>',
        'id' => 'item-category-edit-modal',
    ]); ?>
    <div id="item-category-edit-content"></div>
    <?php Modal::end(); ?>

<script>
    $(document).ready(function() {
        $(document).on('click', '#create-item-category, #item-category-listing  tbody > tr', function () {
            var itemId = $(this).data('key');
            if (itemId === undefined) {
                var customUrl = '<?= Url::to(['item-category/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['item-category/update']); ?>?id=' + itemId;
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
                        $('#item-category-edit-content').html(response.data);
                        $('#item-category-edit-modal').modal('show');
                    } else {
                        $('#error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#update-item-category-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#item-category-listing', timeout: 6000});
                        $('#item-category-edit-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.item-category-cancel', function () {
            $('#item-category-edit-modal').modal('hide');
            return false;
        });
    });
</script>
