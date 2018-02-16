<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogs';
$this->params['action-button'] = Html::a('<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<?php yii\widgets\Pjax::begin(['id' => 'blog']); ?>
<div class="grid-row-open p-20">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) {
                   },
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
<div id="blog-content"></div>
 <?php  Modal::end(); ?>
 <script>
    $(document).ready(function() {
        $(document).on('click', '.action-button, #blog tbody > tr', function () {
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
                        $('#blog-content').html(response.data);
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
                        $.pjax.reload({container: '#blog', timeout: 6000});
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
