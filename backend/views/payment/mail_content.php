<?= $emailTemplate->header ?? 'Please find the payment below:'; ?><Br>
<?php $lessonCount = $lessonDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
	<table style="width:100%">
    	<tr>
			<td>
    			<?= $this->render('/payment/_lesson-line-item', [
            		'lessonDataProvider' => $lessonDataProvider,
	    			'searchModel' => $searchModel,
					'model' => $model,
					'canEdit' => false,
        		]); ?>
			</td>
    	</tr>
	</table>
<?php endif; ?>
<div class="row">
    <!-- /.col -->
    <div class="table-responsive">
	<?php $invoiceCount = $invoiceDataProvider->getCount(); ?>
        <?php if ($invoiceCount > 0) : ?>
            <table class="table table-invoice-total" style="width: 100%;">
            <tbody>
            	<tr>
					<td>
    					<?= $this->render('/payment/_invoice-line-item', [
            				'invoiceDataProvider' => $invoiceDataProvider,
	    					'searchModel' => $searchModel,
							'model' => $model,
							'canEdit' => false,
        				]); ?>
					</td>
    			</tr>
            </tbody>
            </table>
		<?php endif; ?>   

	</div>
</div>
<div class="table-responsive">
            <table class="table table-invoice-total" style="width: 100%;">
            <tbody>
            	<tr>
					<td>
    					<?= $this->render('/payment/_credits-available', [
							'model' => $model,
        				]); ?>
					</td>
    			</tr>
            </tbody>
            </table>		
	</div>
</div>
        <!-- /.col -->
<br>
<?= $emailTemplate->footer ?? 'Thank you 
Arcadia Academy of Music Team.'; ?>
