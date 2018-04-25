<?php

use yii\bootstrap\Modal;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>

<?php yii\widgets\Pjax::begin([
    'id' => 'enrolment-grid',
    'timeout' => 6000,
]) ?>	
<div class="col-md-12">
<?php
    $toolBoxHtml = $this->render('_button', [
        'model' => $model,
         'enrolmentSearchModel'=>$enrolmentSearchModel,
     ]);
        LteBox::begin([
            'type' => LteConst::TYPE_DEFAULT,
            'boxTools' => $toolBoxHtml,
            'title' => 'Enrolments',
            'withBorder' => true,
        ])
        ?>
	<?= $this->render('_list', [
        'enrolmentDataProvider' => $enrolmentDataProvider,
    ]); ?>
   		<?php LteBox::end() ?> 
    </div>
    <?php \yii\widgets\Pjax::end(); ?>

<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Delete Enrolment Preview</h4>',
        'id' => 'enrolment-preview-modal',
    ]);
    Modal::end();
?>

<?php Modal::begin([
    'header' => $this->render('_group-modal-header'),
    'id' => 'group-enrol-modal',
]); ?>

<div id="group-course-content"></div>

<?php Modal::end(); ?>
