<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-index">


    <p>
        <?php echo Html::a('Create Item Category', [''], ['class' => 'btn btn-success', 'id' => 'create-item-category']) ?>
    </p>
<?php Pjax::begin([
    'id' => 'item-category-listing',
    'timeout' => 6000,
]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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
