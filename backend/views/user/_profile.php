<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php 
$boxTools = ['<i title="Edit" class="fa fa-pencil user-edit-button m-r-10"></i>'];?>
<?php if ($model->isCustomer()) : ?>
<?php $merge[] = '<i title="Merge" id="customer-merge" class="fa fa-chain"></i>';
$boxTools = array_merge($boxTools, $merge);
?>
<?php endif;?>
<?php Pjax::begin([
    'id' => 'user-profile'
]); ?>
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
	</dl>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>