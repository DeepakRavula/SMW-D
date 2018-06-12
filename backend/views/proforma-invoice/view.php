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

<div class="row">
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
	<div class="col-md-4">
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
			url    : '<?=Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_PROFORMA]);?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                   alert('sucess');
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
</script>