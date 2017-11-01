<?php

use yii\helpers\Html;
use common\models\UserContact;
?>
<?= Html::hiddenInput('id', $address->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_ADDRESS, ['class' => 'contactType']);?>
<div class="<?= !empty($address->userContact->isPrimary) ? 'primary' : 'not-primary'; ?>">
<dl class="dl-horizontal">
	<dt><?= $address->userContact->label->name; ?></dt>
	<dd><?= !empty($address->address) ? $address->address : null ?></dd>
	<dd> <?= !empty($address->city->name) ? $address->city->name : null . ', ' . !empty($address->province->name) ? $address->province->name : null ?></dd>
	<dd><?= !empty($address->country->name) ? $address->country->name : null ?> </dd>
	<dd><?= !empty($address->postal_code) ? $address->postal_code : null ?></dd>
</dl>
</div>