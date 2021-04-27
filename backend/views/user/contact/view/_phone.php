<?php

use yii\helpers\Html;
use common\models\UserContact;

?>
<?= Html::hiddenInput('id', $phoneNumber->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_PHONE, ['class' => 'contactType']);?>
<div id="<?=$phoneNumber->userContactId ?>" class="<?= !empty($phoneNumber->userContact->isPrimary) ? 'primary' : 'not-primary'; ?> user-phone-edit user-contact-list">
<dl class="dl-horizontal">
	<dt><?= $phoneNumber->userContact->label->name; ?></dt>
	<dd><?= $phoneNumber->number. " (". $phoneNumber->note.")"; ?> </dd>
	<dd><?= !empty($phoneNumber->extension) ? 'Ext: ' . $phoneNumber->extension : null; ?></dd>
</dl>
</div>