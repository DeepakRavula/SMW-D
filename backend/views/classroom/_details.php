<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
    'id' => 'classroom-details'
]); ?>
	<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => '<i title="Edit" class="fa fa-pencil" id="edit-classroom"></i>',
        'title' => 'Details',
        'withBorder' => true,
    ])
    ?>
	<dl class="dl-horizontal">
		<dt>Name</dt>
		<dd><?= $model->name; ?></dd>
        <dt> Long Name</dt>
		<dd><?= $model->description; ?></dd>
	</dl>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>