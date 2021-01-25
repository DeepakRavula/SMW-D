<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="item-category-index">

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<?php Pjax::begin([
    'id' => 'item-category-listing',
    'timeout' => 6000,
]) ?>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',
        ],
        'toolbar' => [
            [
                'content' =>
                    Html::a('<i class="fa fa-plus"></i>', '#', [
                        'class' => 'btn btn-success', 'id' => 'create-item-category'
                    ]),
                'options' => ['title' =>'Add',
                'class' => 'btn-group mr-2']
                ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Item Categories'
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '#create-item-category, #item-category-listing  tbody > tr', function () {
            var itemId = $(this).data('key');
            if (itemId === undefined) {
                var customUrl = '<?= Url::to(['item-category/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['item-category/update']); ?>?id=' + itemId;
		 var url = '<?= Url::to(['item-category/delete']); ?>?id=' + itemId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action",url);
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Item Category</h4>');
			$('#popup-modal .modal-dialog').css({'width': '400px'});
                        $('#modal-content').html(response.data);
                    } else {
                        $('#error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        });
	$(document).on('modal-success', function(event,params) {
	    $.pjax.reload({container: '#item-category-listing', replace:false,async: false, timeout: 6000});
		  });
	$(document).on('modal-delete', function(event, params) {
            $.pjax.reload({container: '#item-category-listing', replace:false,async: false, timeout: 6000});
    });
    });
</script>
