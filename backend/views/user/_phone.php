<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use kartik\sortinput\SortableInput;
?>
<?php Pjax::begin([
	'id' => 'user-phone'
]); ?>
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => '<i title="Add Phone" class="fa fa-plus add-phone-btn"></i>',
		'title' => 'Phone',
		'withBorder' => true,
	])
	?>
	<?php if(!empty($model->phoneNumbers)) : ?>
	<?php $phones = [];?>
		<?php foreach($model->phoneNumbers as $key => $phoneNumber) : ?>		
		<?php 
            $phone = [
				'content' => $this->render('contact/_phone-list', [
				'phoneNumber' => $phoneNumber,
			])];
            array_push($phones, $phone);
			if($phoneNumber->userContact->isPrimary) {
				$value = $phones[$key];
				unset($phones[$key]);
				array_unshift($phones, $value);	
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
		'name'=> 'user_phone',
		'items' => $phones,
		'options' => [
			'class'=>'form-control', 'readonly'=>true]
	]);?>	
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>