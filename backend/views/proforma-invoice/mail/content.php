<?php use yii\bootstrap\Html; ?>
<?= $emailTemplate->header ?? 'Please find the proforma invoice below:'; ?><Br>
<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>
	<table style="width:100%">
    	<tr>
			<td>
    			<?= $this->render('/receive-payment/_lesson-line-item', [
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
		<?= Html::label('Invoices', ['class' => 'admin-login']) ?>
            <table class="table table-invoice-total" style="width: 100%;">
            <tbody>
            	<tr>
					<td>
    					<?= $this->render('/receive-payment/_invoice-line-item', [
            				'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	    					'searchModel' => $searchModel,
            				'model' => $model,
        				]); ?>
					</td>
    			</tr>
            </tbody>
            </table>
		<?php endif; ?>
	      	<table align="right">
		 	<tbody>
		      	<tr>
			  		<td><strong>Total</strong></td>
			  		<td style="text-align:right">
			      	<?=
			      	Yii::$app->formatter->format(
						round($model->total, 2), ['currency', 'USD', [
				      		\NumberFormatter::MIN_FRACTION_DIGITS => 2,
				      		\NumberFormatter::MAX_FRACTION_DIGITS => 2,
				  	]]
			      	);
			      	?>
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
<div><?= $model->reminderNotes; ?></div>