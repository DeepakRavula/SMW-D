<?php

use yii\helpers\Html;
use common\models\UserContact;

?>
<?= Html::hiddenInput('id', $email->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_EMAIL, ['class' => 'contactType']);?>
<div id="<?= $email->userContactId; ?>" class="<?= !empty($email->userContact->isPrimary) ? 'primary' : 'not-primary'; ?> user-email-edit user-contact-list">
<dl class="dl-horizontal">
    <dt><?= $email->userContact->label->name; ?></dt>
	<dd><?= $email->email. ($email->note ? " (". $email->note.")" : ""); ?></dd>
      
</dl>
</div>
