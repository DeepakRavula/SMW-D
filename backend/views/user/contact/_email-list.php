<?php

use yii\helpers\Html;
use common\models\UserContact;

?>
<?= Html::hiddenInput('id', $email->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_EMAIL, ['class' => 'contactType']);?>
<div class="<?= !empty($email->userContact->isPrimary) ? 'primary' : 'not-primary'; ?>">
<dl class="dl-horizontal">
	<dt><?= $email->userContact->label->name; ?></dt>
	<dd><?= $email->email; ?></dd>
</dl>
</div>
