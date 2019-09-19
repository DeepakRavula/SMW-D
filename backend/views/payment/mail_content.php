<?= $emailTemplate->header ?? 'Please find the payment below:'; ?><Br>
<div>
    <h4 class = "payment-receipt">This is to acknowledge the receipt of payment from <?= $model->userProfile->firstname .' '.$model->userProfile->lastname; ?> on <?= Yii::$app->formatter->asDate($model->date); ?> 
in the amount of <?= $model->amount; ?> via <?= $model->paymentMethod->name; ?>. We have distributed it to the items below.</h4>
</div>
<?php $lessonCount = $lessonDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
<div class="m-l-22"> <b>Lessons</b></div>
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
<?php $lessonCount = $groupLessonDataProvider->getCount(); ?>
<?php if ($lessonCount > 0) : ?>
<div class="m-l-22"> <b>Group Lessons</b></div>
	<table style="width:100%">
    	<tr>
			<td>
    			<?= $this->render('/payment/_group-lesson-line-item', [
            		'lessonDataProvider' => $groupLessonDataProvider,
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
		<div class="m-l-22"> <b>Invoices</b></div>
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
<div class="m-l-22"> <b>Payments Used</b></div>
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
