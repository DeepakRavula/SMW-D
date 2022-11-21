<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?> 
<div class="blog-index">  
<?php yii\widgets\Pjax::begin(['id' => 'blog-listing']); ?>
<?= KartikGridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
            [
                'label' => 'User Name',
                'value' => function ($data) {
                    return $data->user->publicIdentity;
                },
            ],
            [
                'label' => 'Title',
                'format' => 'raw',
                'value' => function ($data) {
                    return substr($data->title, 0, 25).' ...';
                },
            ],
            [
                'label' => 'Content',
                'format' => 'raw',
                'value' => function ($data) {
                    return substr($data->content, 0, 25).' ...';
                },
            ],
            'date:date',
        ],
        'toolbar' => [
            ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
                'class' => 'btn btn-success new-blog'
            ]),'options' => ['title' =>'Add',
            'class' => 'btn-group mr-2']],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Blogs'
        ],
    ]); ?>

<?php yii\widgets\Pjax::end(); ?>
    </div>
  <script>
        $(document).on('click', '.new-blog,#blog-listing  tbody > tr', function () {
            var blogId = $(this).data('key');
             if (blogId === undefined) {
                    var customUrl = '<?= Url::to(['blog/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['blog/update']); ?>?id=' + blogId;
                var url = '<?= Url::to(['blog/delete']); ?>?id=' + blogId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action",url);
            }
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Blogs</h4>');
			            $('#popup-modal .modal-dialog').css({'width': '1000px'});
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>