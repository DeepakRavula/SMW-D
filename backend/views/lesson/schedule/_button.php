<?php
use yii\helpers\Url;

?>
<i title="Edit" class="m-r-10 fa fa-pencil edit-lesson-schedule"></i>
<?php if ($model->isScheduled()) : ?>
	<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="lesson-unschedule" href="#">Unschedule Lesson</a></li>
            
            <?php if ($model->isPrivate()) : ?>
            <?php if (!$model->invoice) : ?>
		<li>
                    <a href= "<?= Url::to(['lesson/invoice', 'id' => $model->id]); ?>">
                        Generate Invoice
                    </a>
                </li>
            <?php else : ?>
                <li>
                    <a href= "<?= Url::to(['invoice/view', 'id' => $model->invoice->id]); ?>">
                        View Invoice
                    </a>
                </li>
            <?php endif; ?>
            <?php if (!$model->proFormaInvoice): ?>
		<li>
                    <a href= "<?= Url::to(['lesson/take-payment', 'id' => $model->id]) ?>">
                        Generate PFI
                    </a>
                </li>
            <?php elseif (!$model->proFormaInvoice->isPaid()) : ?>
                <li>
                    <a href= "<?= Url::to(['lesson/take-payment', 'id' => $model->id]) ?>">
                        View PFI
                    </a>
                </li>
            <?php endif; ?>
                <?php endif; ?>
        </ul>
<?php endif; ?>