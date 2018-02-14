<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Templates';
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="student-index">  
<?php yii\widgets\Pjax::begin(['id' => 'mail-template']); ?>
<?php
echo AdminLteGridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Type',
            'value' => function ($data) {
                return $data->emailObject->name;
            },
        ],
        [
            'label' => 'Subject',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->subject;
            },
        ],
        [
            'label' => 'Header',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->header;
            },
        ],
        [
            'label' => 'Footer',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->footer;
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
    </div>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Email Template</h4>',
        'id' => 'email-template-modal',
    ]); ?>
<div id="email-template-content"></div>
 <?php  Modal::end(); ?>
  <script>
    $(document).ready(function() {
        $(document).on('click', '#mail-template  tbody > tr', function () {
            var emailId = $(this).data('key');
                var customUrl = '<?= Url::to(['email-template/update']); ?>?id=' + emailId;
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#email-template-content').html(response.data);
                        $('#email-template-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#email-template-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#mail-template', timeout: 6000});
                        $('#email-template-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.template-cancel', function () {
            $('#email-template-modal').modal('hide');
            return false;
        });
    });
</script>