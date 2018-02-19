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
<?php Modal::begin([
        'header' => '<h4 class="m-0">Blog</h4>',
        'id' => 'blog-modal',
    ]); ?>
<div id="blog-contents"></div>
 <?php  Modal::end(); ?>
  <script>
    $(document).ready(function() {
        $(document).on('click', '.action-button,#blog-listing  tbody > tr', function () {
            var blogId = $(this).data('key');
             if (blogId === undefined) {
                    var customUrl = '<?= Url::to(['blog/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['blog/update']); ?>?id=' + blogId;
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
                        $('#blog-contents').html(response.data);
                        $('#blog-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#blog-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#blog-listing', timeout: 6000});
                        $('#blog-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.blog-cancel', function () {
            $('#blog-modal').modal('hide');
            return false;
        });
    });
</script>