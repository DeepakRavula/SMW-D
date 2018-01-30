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
	</dl>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>