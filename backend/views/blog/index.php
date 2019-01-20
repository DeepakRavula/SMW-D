<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogs';
$this->params['breadcrumbs'][] = $this->title;
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'new-blog']);
?> 
<div class="blog-index">  
<?php yii\widgets\Pjax::begin(['id' => 'blog-listing']); ?>
<?php
echo AdminLteGridView::widget([
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
    ]); ?>

<?php yii\widgets\Pjax::end(); ?>
    </div>
  <script>
        $(document).on('click', '.action-button,#blog-listing  tbody > tr', function () {
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