<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Frequencies';
?>
<div class="payment-frequency-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',

            [
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update}',
				'buttons' => [
					'update' => function  ($url, $model) {
	                    return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
							'class' => 'discount-edit m-l-20'
						]);
					},
				],
			],
        ],
    ]); ?>

</div>
<script>
$(document).ready(function() {
$(document).on('click', '.discount-edit' ,function() {
		$.ajax({
			url    : '<?= Url::to(['payment-frequency/update']); ?>?id=' + $(this).parent().parent().data('key'),
			type   : 'get',
			dataType: "json",
			success: function(response)
			{
			   if(response.status)
			   {
				   $('#new-exam-result-modal .modal-body').html(response.data);
					$('#new-exam-result-modal').modal('show');
				} else {
				 $('#lesson-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
	});
});
</script>