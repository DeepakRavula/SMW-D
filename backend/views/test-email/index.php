<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Email';
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="student-index">  
<?php yii\widgets\Pjax::begin(['id' => 'test-email-listing']); ?>
<?php
echo AdminLteGridView::widget([
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'ID',
            'value' => function ($data) {
                return $data->id;
            },
        ],
        [
            'label' => 'Email',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->email;
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
    </div>
<script>
        $(document).on('click', '#test-email-listing  tbody > tr', function () {
            var testEmailId = $(this).data('key');
                var customUrl = '<?= Url::to(['test-email/update']); ?>?id=' + testEmailId;
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Test Email</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>