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
    <dt>Student</dt>
    <dd>
        <a href= "<?= Url::to(['student/view', 'id' => $model->student->id]) ?>">
            <?= $model->student->fullName; ?>
        </a></dd>
    <dt>Customer</dt>
    <dd>
        <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER,
                'id' => $model->student->customer->id]) ?>">
            <?= $model->student->customer->publicIdentity; ?>
        </a>
    </dd>
    <dt>Phone</dt>
    <dd><?= !empty($model->student->customer->phoneNumber->number) ? $model->student->customer->phoneNumber->number
                    : 'None'; ?></dd>
</dl>
<?php LteBox::end() ?>
