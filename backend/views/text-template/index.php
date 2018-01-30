<?php

use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Text Templates';
$this->params['breadcrumbs'][] = $this->title;
?> 
<div class="student-index">  
<?php yii\widgets\Pjax::begin(['id' => 'text-template']); ?>
<?php
echo AdminLteGridView::widget([
    'id' => 'template-grid',
    'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Type',
            'value' => function ($data) {
                return $data->getType();
            },
        ],
        [
            'label' => 'Message',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->message;
            },
        ],
    ],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
    </div>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Text Template</h4>',
        'id' => 'template-modal',
    ]); ?>
<div id="template-content"></div>
 <?php  Modal::end(); ?>
<script>
$(document).ready(function(){
  $(document).on('click', '#template-grid  tbody > tr', function () {
		var templateId = $(this).data('key');
		var url = '<?= Url::to(['text-template/update']); ?>?id=' + templateId;
		$.ajax({
			url    : url,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status)
				{
					$('#template-content').html(response.data);
					$('#template-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#template-form', function () {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status) {
					$.pjax.reload({container: '#text-template', timeout: 6000});
					$('#template-modal').modal('hide');
				}
			}
		});
		return false;
	});
        $(document).on('click', '.template-cancel', function () {
            $('#template-modal').modal('hide');
            return false;
        }); 
});
  </script>