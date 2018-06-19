<?php
//print_r($model->id);die;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Note;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = 'Proforma Invoice';
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = $this->render('_buttons', [
    'model' => $model,
]);?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Message</h4>',
    'id' => 'message-modal',
]); ?>
<?= $this->render('note/_form', [
    'model' => $model,
]); ?>
<?php Modal::end();?>
<div class="row m-t-25">
	<div class="col-md-6">
		<?=
$this->render('_details', [
    'model' => $model,
]);
?>
	</div>
    <?php if (!empty($customer)): ?>
	<div class="col-md-6">
		<?=
$this->render('_customer', [
    'model' => $model,
    'customer' => $customer,
]);
?>
	</div>

	<?php endif;?>
</div>
<div class="row">
<div class="col-md-12">
<?php LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '',
    'title' => 'Lessons',
    'withBorder' => true,
])
?>
<?=
$this->render('/receive-payment/_lesson-line-item', [
    'model' => $model,
    'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
    'searchModel' => $searchModel,
]);
?>
        <?php LteBox::end()?>
        </div>

</div>
<div class="row">
<div class="col-md-12">
<?php LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '',
    'title' => 'Invoices',
    'withBorder' => true,
])
?>
<?=
$this->render('/receive-payment/_invoice-line-item', [
    'model' => $model,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
    'searchModel' => $searchModel,
]);
?>

         <?php LteBox::end()?>
        </div>
</div>

<div class="row">
<?php Pjax::Begin(['id' => 'invoice-message-panel', 'timeout' => 6000]); ?>
   <div class="col-md-3">
		<?=
         $this->render('_message', [
            'model' => $model,
        ]);
        ?>
	</div>
    <?php Pjax::end(); ?>
	<div class="col-md-4">
		<?=
$this->render('note/view', [
    'model' => new Note(),
    'noteDataProvider' => $noteDataProvider,
]);
?>
	</div>
<?php Pjax::Begin(['id' => 'invoice-user-history', 'timeout' => 6000]); ?>
	<div class="col-md-5">
		<?=
        $this->render('log', [
            'model' => $model,
        ]);
        ?>	
	</div>
	<?php Pjax::end(); ?>
</div>
<script>
      	$(document).on('beforeSubmit', '#invoice-note-form', function (e) {
		$.ajax({
			url    : '<?=Url::to(['proforma-invoice/note']);?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                   
					$('.invoice-note-content').html(response.data);
					$('#invoice-note-modal').modal('hide');
				}else
				{
				 $('#invoice-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
	$(document).on('click', '.add-invoice-note', function (e) {
		$('#message-modal').modal('show');
		return false;
  	});
	  $(document).on('beforeSubmit', '#invoice-message-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container: '#invoice-message-panel', replace:false, timeout: 6000});
					$('#message-modal').modal('hide');
				}
			}
		});
		return false;
	});
</script>