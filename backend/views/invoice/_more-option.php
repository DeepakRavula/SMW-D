<div class="btn-group-sm">
    <button class="btn dropdown-toggle" data-toggle="dropdown">More Action&nbsp;&nbsp;<span class="caret"></span></button>
    <ul class="dropdown-menu dropdown-menu-right">
        <?php if ($model->isInvoice()) : ?>
             <?php if (!$model->isVoid) : ?>
                <li><a id="void" href="#">Void</a></li>
            <?php else : ?>
                <li><a class="multiselect-disable" href="#">Void</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>