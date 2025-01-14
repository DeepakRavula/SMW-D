<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use common\Models\User;
?>
<?php 
$boxTools = ['<i title="Edit" class="fa fa-pencil user-edit-button m-r-10"></i>'];?>
<?php $loggedUser = User::findOne(Yii::$app->user->id); ?>

<?php if ($model->isCustomer()) : ?>
        <?php $merge[]  = '<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i><ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
		<li><a id="customer-merge" href="#" >Merge</a></li>
		<li><a id="user-change-password" href="#" >Set Password</a></li>
    </ul>'; ?>
        <?php $boxTools = array_merge($boxTools, $merge); ?>
<?php endif;?>

<?php if ($model->isTeacher()) : ?>
        <?php $merge[]  = '<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i><ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
		<li><a id="user-change-password" href="#" >Set Password</a></li>
    </ul>'; ?>
        <?php $boxTools = array_merge($boxTools, $merge); ?>
<?php endif;?>

<?php if ($model->isStaff()) : ?>
        <?php $merge[]  = '<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i><ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
		<li><a id="user-change-password" href="#" >Set Password</a></li>
    </ul>'; ?>
        <?php $boxTools = array_merge($boxTools, $merge); ?>
<?php endif;?>

<?php if ($model->isAdmin()) : ?>
        <?php $merge[]  = '<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i><ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
		<li><a id="user-change-password" href="#" >Set Password</a></li>
    </ul>'; ?>
        <?php $boxTools = array_merge($boxTools, $merge); ?>
<?php endif;?>

<?php if ($model->isOwner()) : ?>
        <?php $merge[]  = '<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i><ul class="dropdown-menu dropdown-menu-right" id="menu-shown">
		<li><a id="user-change-password" href="#" >Set Password</a></li>
    </ul>'; ?>
        <?php $boxTools = array_merge($boxTools, $merge); ?>
<?php endif;?>

	<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Details',
        'withBorder' => true,
    ])
    ?>
	<dl class="dl-horizontal">
		<dt>Name</dt>
		<dd><?= $model->publicIdentity; ?></dd>
		<dt>Role</dt>
		<dd><?= $role; ?></dd>
		<?php if($model->isTeacher()) : ?>
		<dt>Birth Date</dt>
		<dd><?= Yii::$app->formatter->asDate($model->userProfile->birthDate); ?></dd>
		<?php endif;?>
        <?php if($model->isCustomer()) : ?>
		<dt>Referral Source</dt>
		<dd><?=  $model->customerReferralSource ? $model->customerReferralSource->referralSource->isOther() ? $model->customerReferralSource->description : $model->customerReferralSource->referralSource->name: null; ?></dd>
        <dt>Status</dt>
		<dd><?= $model->getStatus(); ?></dd>
		<?php endif;?>
	</dl>
	<?php LteBox::end() ?>
