<?php

use yii\helpers\Html;
use common\models\UserContact;
?>
<?= Html::hiddenInput('id', $phoneNumber->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_PHONE, ['class' => 'contactType']);?>
<div class="<?= !empty($phoneNumber->userContact->isPrimary) ? 'primary' : 'not-primary'; ?>">
<dl class="dl-horizontal">
	<dt><?= $phoneNumber->userContact->label->name; ?></dt>
	<dd><?= $phoneNumber->number; ?> <?php echo' <i title="Edit" id="'.$phoneNumber->userContactId.'" class="fa fa-pencil user-phone-edit m-l-10"></i>';?></dd>
	<dd><?= !empty($phoneNumber->extension) ? '<strong>Ext:</strong> ' . $phoneNumber->extension : null; ?></dd>
</dl>
</div>