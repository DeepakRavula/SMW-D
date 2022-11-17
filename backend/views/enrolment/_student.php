<?php

use yii\helpers\Url;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Student',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">

    <dt>Phone</dt>
    <dd><?= $model->student->customer->phoneNumber->number ?? null; ?></dd>
</dl>
<?php LteBox::end() ?>
