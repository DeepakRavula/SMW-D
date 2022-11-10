<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $this->render('_summary-button', ['model' => $model]),
        'title' => 'Totals',
        'withBorder' => true,
    ])
    ?>
<?=$this->render('/invoice/_bottom-summary-list', [
            'model' => $model,
        ]);?>
<?php LteBox::end() ?>
