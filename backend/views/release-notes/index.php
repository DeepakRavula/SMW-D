<?php

use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Release Notes';
$this->params['action-button'] = Html::a('<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i> Create', ['#'], ['class' => 'add-release-notes']);
$this->params['breadcrumbs'][] = $this->title;
?> 
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Release Notes</h4>',
        'id'=>'new-release-notes-modal',
    ]);
    Modal::end();
    ?>		

<div class="release-notes-index "> 
    <?php Pjax::begin([
        'id' => 'release-notes-listing',
        'timeout' => 6000
    ]); ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'notes:raw',
            'date:date',
            'user.publicIdentity',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?> 
<?php Pjax::end(); ?>
</div> 
<script>
$(document).ready(function () {
    $(document).on("click", ".add-release-notes", function() {
            $.ajax({
                url    : '<?= Url::to(['release-notes/create']); ?>',
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#new-release-notes-modal .modal-body').html(response.data);
                        $('#new-release-notes-modal .modal-dialog').css({'width': '900px'});
                        $('#new-release-notes-modal').modal('show');
                    } else {
                        $('#release-notes-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            
        return false;
    });
    $(document).on('beforeSubmit', '#new-release-notes-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#release-notes-listing', timeout: 6000});
                        $('#new-release-notes-modal').modal('hide');
                    } else {
                        $('#release-notes-form').yiiActiveForm('updateMessages',
                                response.errors, true);
                    }
                }
            });
            return false;
        });
    });
</script>