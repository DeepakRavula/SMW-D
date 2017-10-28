<?php
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use kartik\sortinput\SortableInput;

?>
<?php Pjax::begin([
	'id' => 'user-address'
]); ?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-plus add-address-btn"></i>',
	'title' => 'Addresses',
	'withBorder' => true,
])
?>
<?php if(!empty($model->addresses)) : ?>
	<?php $addresses = [];?>
		<?php foreach($model->addresses as $key => $userAddress) : ?>		
		<?php 
            $address = [
				'content' => $this->render('contact/_address-list', [
				'address' => $userAddress,
			])];
            array_push($addresses, $address);
			if($userAddress->userContact->isPrimary) {
				$value = $addresses[$key];
				unset($addresses[$key]);
				array_unshift($addresses, $value);	
			}
        ?>
	<?php endforeach; ?>
	<?= SortableInput::widget([
		'sortableOptions' => [
			'type' => 'list',
			'pluginEvents' => [
				'sortupdate' => 'contact.updatePrimary',
			],
		],
		'name'=> 'user_address',
		'items' => $addresses,
		'options' => [
			'class'=>'form-control', 'readonly'=>true]
	]);?>	
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>
