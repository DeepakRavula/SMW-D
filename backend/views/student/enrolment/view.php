<?php

use yii\bootstrap\Modal;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
?>

<?php yii\widgets\Pjax::begin([
    'id' => 'enrolment-list-student',
    'timeout' => 6000,
    'enablePushState'=>false,
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

<script>
    $(document).on("click", "#enrolmentsearch-showallenrolments", function() {
        var showAllEnrolments = $(this).is(":checked");
        var params=$.param({ 'EnrolmentSearch[showAllEnrolments]':(showAllEnrolments | 0),'EnrolmentSearch[studentView]':1,'EnrolmentSearch[studentId]':<?= $model->id ?>,});
        var url = "<?php echo Url::to(['student/view', 'id' => $model->id]); ?>&"+params;
        $.pjax.reload({url: url,container: "#student-enrolment-list", replace: false, timeout: 6000});  //Reload GridView //Reload GridView
    });
</script>