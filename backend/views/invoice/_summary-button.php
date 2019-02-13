<?php if ($model->isInvoice()) : ?>
    <i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
    <ul class="dropdown-menu dropdown-menu-right">
        <?php if ($model->lineItem) : ?>
            <?php if (!$model->lineItem->isOpeningBalance() && !$model->isPaymentCreditInvoice()) : ?>
            <li><a class="adjust-invoice-tax" href="#">Adjust Tax</a></li>
            <?php else: ?>
            <li><a class="adjust-invoice-tax multiselect-disable" href="#">Adjust Tax</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a class="adjust-invoice-tax multiselect-disable" href="#">Adjust Tax</a></li>
        <?php endif; ?>
    </ul>
<?php endif; ?>