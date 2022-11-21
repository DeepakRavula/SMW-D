<div class="lesson-note-content">
<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Comments',
    'withBorder' => true,
])
?>
<?=
    $this->render('_view', [
        'noteDataProvider' =>  $noteDataProvider,
        'model' => $model,
]);
?>
  <?php LteBox::end() ?>

</div>
