<?php
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
	'id' => 'user-address'
]); ?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil user-address-btn"></i>',
	'title' => 'Addresses',
	'withBorder' => true,
])
?>
<?php if(!empty($model->addresses)) : ?>
	<?php foreach($model->addresses as $address) : ?>
<div class="address p-t-9 p-b-10 relative">
    <div class="col-md-2 p-0"><strong><?= !empty($address->label) ? $address->label : null ?></strong></div> 
    <div class="<?= !empty($address->is_primary) ? 'primary' : null; ?>">
		<div class="col-md-9">
    	<?= !empty($address->address) ? $address->address : null ?> <Br>
        <?= !empty($address->city->name) ? $address->city->name : null ?>,
		 <?= !empty($address->province->name) ? $address->province->name : null ?><Br> 
        <?= !empty($address->country->name) ? $address->country->name : null ?> 
		<?= !empty($address->postal_code) ? $address->postal_code : null ?>
    </div>
	</div>
    <div class="clearfix"></div>
</div>
	<?php endforeach; ?>
<?php endif; ?>
<?php LteBox::end() ?>
<?php Pjax::end(); ?>