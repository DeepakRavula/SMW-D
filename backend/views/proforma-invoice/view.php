<?php

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
    
    <?php
    $lessonCount = $lessonLineItemsDataProvider->getCount();
    if ($lessonCount > 0) {
	    ?>
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
    <?php } ?>
        </div>

</div>
<div class="row">
<div class="col-md-12">
    <?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); 
    if($invoiceCount > 0) { ?>
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
    <?php } ?>
        </div>
</div>

<div class="row">
<?php Pjax::Begin(['id' => 'invoice-message-panel', 'timeout' => 6000]); ?>
   <div class="col-md-4">
		<?=
         $this->render('_message', [
            'model' => $model,
        ]);
        ?>
	</div>
    <?php Pjax::end(); ?>
	<div class="col-md-5">
            <?php Pjax::Begin(['id' => 'invoice-user-history', 'timeout' => 6000]); ?>
	
		<?=
        $this->render('log', [
            'model' => $model,
        ]);
        ?>
	<?php Pjax::end(); ?>
	</div>
    	<?php Pjax::Begin(['id' => 'invoice-bottom-summary', 'timeout' => 6000]); ?>
	<div class="col-md-3">
		<?=
        $this->render('_view-bottom-summary', [
            'model' => $model,
        ]);
        ?>
	</div>
</div>
    <?php Pjax::end(); ?>
    <div class="row">
        <div class="col-md-12">
        <?=
$this->render('note/view', [
    'model' => new Note(),
    'noteDataProvider' => $noteDataProvider,
]);
?>
        </div>
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
	$(document).on('click', '#proforma-invoice-mail-button', function (e) {
            $.ajax({
                url    : '<?= Url::to(['email/proforma-invoice', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: 'json',
                success: function(response)
                {
                    if (response.status) {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                    }
                }
            });
            return false;
  	});
	$(document).on("click", '#proforma-print-btn', function() {
        var url = '<?= Url::to(['print/proforma-invoice' ,'id' => $model->id]); ?>';
        window.open(url,'_blank');
        return false;
    });
</script>