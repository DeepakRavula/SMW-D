<?php

use yii\helpers\Html;
use common\models\UserContact;

?>
<?= Html::hiddenInput('id', $email->userContactId, ['class' => 'contact']);?>
<?= Html::hiddenInput('contactType', UserContact::TYPE_EMAIL, ['class' => 'contactType']);?>
<div class="<?= !empty($email->userContact->isPrimary) ? 'primary' : 'not-primary'; ?>">
<dl class="dl-horizontal">
    <dt><?= $email->userContact->label->name; ?></dt>
	<dd><?= $email->email; ?>  <?php echo' <i title="Edit" id="'.$email->userContactId.'" class="fa fa-pencil user-email-edit m-l-10"></i>';?></dd>
      
</dl>
</div>
