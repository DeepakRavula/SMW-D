<?= $emailTemplate->header ?? 'Please find the Receipt below:'; ?><Br>
<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
	<table style="width:100%">
    	<tr>
			<td>
    			<?= $this->render('/receive-payment/print/_lesson-line-item', [
            		'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
	    			'searchModel' => $searchModel,
            		'model' => $model,
        		]); ?>
			</td>
    	</tr>
	</table>
<?php endif; ?>
<div class="row">
    <!-- /.col -->
    <div class="table-responsive">
	<?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); ?>
        <?php if ($invoiceCount > 0) : ?>
            <table class="table table-invoice-total" style="width: 100%;">
            <tbody>
            	<tr>
					<td>
    					<?= $this->render('/receive-payment/print/_invoice-line-item', [
            				'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	    					'searchModel' => $searchModel,
            				'model' => $model,
        				]); ?>
					</td>
    			</tr>
            </tbody>
            </table>
		<?php endif; ?> 
	</div>
</div>
        <!-- /.col -->
<br>
<?= $emailTemplate->footer ?? 'Thank you 
Arcadia Academy of Music Team.'; ?>
