<?php

use yii\helpers\Url;
use common\models\User;

?>
<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<?php if (empty($model->user)) : ?>
		<li><a class="add-customer" href="<?= Url::to(['invoice/fetch-user', 'id' => $model->id, "UserSearch[role_name]" => User::ROLE_CUSTOMER])?>">Add Existing Customer...</a></li>
		<li><a class="add-walkin" href="#">Add Walk-in...</a></li>
	<?php elseif ($model->user->isCustomer()) : ?>
		<li><a class="add-customer" href="<?= Url::to(['invoice/fetch-user', 'id' => $model->id, "UserSearch[role_name]" => User::ROLE_CUSTOMER])?>">Change Customer...</a></li>
	<?php elseif ($model->user->isWalkin()) : ?>
		<li><a class="add-walkin" href="#">Edit Walk-in...</a></li>
	<?php endif;?>
</ul>
