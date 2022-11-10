<?php

use yii\helpers\Html;
use common\models\UserContact;

?>
<?= Html::hiddenInput('id', $address->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_ADDRESS, ['class' => 'contactType']);?>
<div id="<?= $address->userContact->id; ?>" class="<?= !empty($address->userContact->isPrimary) ? 'primary' : 'not-primary'; ?> user-address-edit user-contact-list">
<dl class="dl-horizontal">
   <?php  if (!empty($address->city->name)):?>
	<dt><?= $address->userContact->label->name; ?></dt>
	<dd><?= !empty($address->address) ? $address->address : null ?></dd>
	<dd> <?= !empty($address->city->name) ? $address->city->name : null ?>,<?= !empty($address->province->name) ? $address->province->name : null ?></dd>
	<dd><?= !empty($address->country->name) ? $address->country->name : null ?> </dd>
	<dd><?= !empty($address->postal_code) ? $address->postal_code : null ?></dd>
       <?php endif; ?>
</dl>
</div>