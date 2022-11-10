<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Totals',
        'withBorder' => true,
    ])
    ?>
<?=$this->render('_bottom-summary-list', [
            'model' => $model,
        ]);?>
<?php LteBox::end() ?>
