<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use kartik\sortinput\SortableInput;

?>
<?php Pjax::begin([
    'id' => 'user-email'
]); ?>
	<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => '<i title="Add" class="fa fa-plus add-email"></i>',
        'title' => 'Email',
        'withBorder' => true,
    ])
    ?>
	<?php if (!empty($model->emails)) : ?>
	<?php $emails = [];?>
		<?php foreach ($model->emails as $key => $userEmail) : ?>		
		<?php 
            $email = [
                'content' => $this->render('contact/view/_email', [
                'email' => $userEmail,
            ])];
            array_push($emails, $email);
            if ($userEmail->userContact->isPrimary) {
                $value = $emails[$key];
                unset($emails[$key]);
                array_unshift($emails, $value);
            }
        ?>
	<?php endforeach; ?>
	<?= SortableInput::widget([
        'sortableOptions' => [
            'showHandle' => true,
            'handleLabel' =>'<i class="fa fa-arrows"></i>',
            'type' => 'list',
            'pluginEvents' => [
                'sortupdate' => 'contact.updatePrimary',
            ],
        ],
        'name'=> 'user_email',
        'items' => $emails,
        'options' => [
            'class'=>'form-control', 'readonly'=>true]
    ]);?>	
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>