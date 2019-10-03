<?php

use common\models\Note;
use common\models\PrivateLesson;
use common\models\User;
use kartik\select2\Select2Asset;
use kartik\time\TimePickerAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
Select2Asset::register($this);
TimePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->course->program->name;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = $this->render('_more-action-menu', [
    'model' => $model,
]);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<div id="lesson-view-tax" style="display: none;" class="alert-danger alert fade in"></div>
<div id="view-danger-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="success-notification" style="display:none;" class="alert-success alert fade in"></div>
<br>
<div class="m-b-10"> </div>
<div class="row">
	<div class="col-md-6">

		<?=$this->render('_details', [
    'model' => $model,
]);?>

        <?php if (!$model->isGroup()): ?>

            <?=$this->render('_student', [
    'model' => $model,
]);?>

            <?php if ($model->isPrivate() && !$model->isUnscheduled()): ?>
                <div id="attendance-panel">
                    <?=$this->render('attendance/_view', [
    'model' => $model,
]);?>
                </div>
            <?php endif;?>
        <?php endif;?>
        <?php $loggedUser = User::findOne(Yii::$app->user->id);?>
        <?php if ($loggedUser->isAdmin() || $loggedUser->isOwner()): ?>
            <div id="cost-panel">
                <?=$this->render('cost/_view', [
    'model' => $model,
]);?>
            </div>
        <?php endif;?>
    </div>
    <div class="col-md-6">
        <?=$this->render('schedule/_view', [
    'model' => $model,
]);?>
<?php if (!$model->isGroup()): ?>
  <?= $this->render('_due-date', [
    'model' => $model,
]);?>
        
        <?=$this->render('_total-details', [
    'model' => $model,
]);?>
        <?php endif;?>
      
    </div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
				<?php
$paymentContent = $this->render('payment/view', [
    'paymentsDataProvider' => $paymentsDataProvider,
]);
$studentContent = $this->render('student/view', [
    'studentDataProvider' => $studentDataProvider,
    'lessonModel' => $model,
]);
$noteContent = $this->render('note/view', [
    'model' => new Note(),
    'noteDataProvider' => $noteDataProvider,
]);

$logContent = $this->render('log', [
    'model' => $model,
    'logDataProvider' => $logDataProvider,
]);

$privateItem = [
    [
        'label' => 'Payments',
        'content' => $paymentContent,
    ],
];
$groupItem = [
    [
        'label' => 'Students',
        'content' => $studentContent,
    ],
];
$items = [
    [
        'label' => 'Comments',
        'content' => $noteContent,
    ],
    [
        'label' => 'History',
        'content' => $logContent,
    ],
];
if (!$model->isGroup()) {
    $lessonItems = array_merge($privateItem, $items);
} else {
    $lessonItems = array_merge($groupItem, $items);
}
echo Tabs::widget([
    'items' => $lessonItems,
]);
?>
			</div>
		</div>
</div>

<div id="loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>



<?php Modal::begin([
    'header' => '<h4 class="m-0">Payment Details</h4>',
    'id' => 'lesson-payment-modal',
]);?>
<div id="lesson-payment-content"></div>
<?php Modal::end();?>

<?php if ($model->hasExpiryDate()): ?>
	<?php $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);?>
<?php endif;?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Attendance</h4>',
    'id' => 'attendance-modal',
]);?>
<?=$this->render('attendance/_form', [
    'model' => $model,
]);?>
<?php Modal::end();?>

<script>
    $(document).on('click', '.edit-attendance', function () {
        $('#attendance-modal').modal('show');
        $('#attendance-modal .modal-dialog').css({'width': '400px'});
        return false;
    });

    $(document).on('click', '.attendance-cancel', function () {
        $('#attendance-modal').modal('hide');
        return false;
    });

    $(document).on("click", '.mail-view-cancel-button', function() {
        $('#lesson-mail-modal').modal('hide');
        return false;
    });

    $(document).on('click', '#view-payment', function () {
        $.ajax({
            url    : $(this).attr('url'),
            type   : 'get',
            success: function(response)
            {
                if(response.status) {
                    $('#lesson-payment-modal').modal('show');
                    $('#lesson-payment-content').html(response.data);
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#mail-form', function (e) {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#spinner').hide();
                    $('#lesson-mail-modal').modal('hide');
                    $('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });

    $(document).on("click", "#payment-grid tbody > tr", function() {
        var lessonPaymentId = $(this).data('key');
        var params = $.param({'PaymentEditForm[lessonPaymentId]': lessonPaymentId });
        var customUrl = '<?=Url::to(['payment/view']);?>?' + params;
        $.ajax({
            url: customUrl,
            type: 'get',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).off("click", ".edit-cost").on("click", ".edit-cost", function() {
        var customUrl = '<?=Url::to(['lesson/edit-cost', 'id' => $model->id]);?>';
        $.ajax({
            url: customUrl,
            type: 'get',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#lesson-note-form', function (e) {
        $.ajax({
            url    : '<?=Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON]);?>',
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('.lesson-note-content').html(response.data);
                }
            }
        });
        return false;
    });

    $(document).on('click', '#lesson-mail-button', function () {
        $.ajax({
            url    : '<?=Url::to(['email/lesson', 'id' => $model->id]);?>',
            type   : 'get',
            dataType: 'json',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '1000px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Email Preview</h4>');
                    $('.modal-save').show();
                    $('.modal-save').text('Send');
                }
            }
        });
        return false;
    });

    $(document).on('click', '#merge-lesson', function (e) {
        $.ajax({
            url    : '<?=Url::to(['private-lesson/merge', 'id' => $model->id])?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Lesson Merge</h4>');
                    $('.modal-save').show();
                    $('.modal-save').text('Merge');
                } else {
                    $('#merge-error-notification').text(response.error).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        } else {
            if ($('#lesson-cost').length) {
                $.pjax.reload({container: "#lesson-cost", replace: false, async: false, timeout: 6000});
            }
            if ($('#lesson-discount').length) {
                $.pjax.reload({container: "#lesson-discount", replace: false, async: false, timeout: 6000});
            }
            if ($('#lesson-price-details').length) {
                $.pjax.reload({container: "#lesson-price-details", replace: false, async: false, timeout: 6000});
            }
            if ($('#lesson-payment-listing').length) {
                $.pjax.reload({container: "#lesson-payment-listing", replace: false, async: false, timeout: 6000});
            }
            if ($('#group-lesson-discount').length) {
                $.pjax.reload({container: "#group-lesson-discount", replace: false, async: false, timeout: 6000});
            }
        }
        return false;
    });

     $(document).on('modal-next', function(event, params) {
        $.pjax.reload({container: "#lesson-payment-listing", replace: false, async: false, timeout: 6000});
        $.pjax.reload({ container: "#lesson-price-details", replace: false, timeout: 4000});
        return false;
    });

    $(document).on('beforeSubmit', '#attendance-form', function (e) {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: 'json',
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $.pjax.reload({container: '#lesson-attendance', replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: '#lesson-detail', replace: false, async: false, timeout: 6000});
                    $('#attendance-modal').modal('hide');
                }
            }
        });
        return false;
    });

    $(document).on('click', '#lesson-delete', function () {
        var id = '<?=$model->id;?>';
        var params = $.param({ 'PrivateLesson[ids]': [id], 'PrivateLesson[isBulk]': false });
        bootbox.confirm({
            message: "Are you sure you want to delete this lesson?",
            callback: function(result){
                if(result) {
                    $('.bootbox').modal('hide');
                    $.ajax({
                        url: '<?=Url::to(['private-lesson/delete']);?>?' + params,
                        type: 'post',
                        success: function (response)
                        {
                            if (response.status)
                            {
                                window.location.href = response.url;
                                $('#index-success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                            } else {
                                $('#view-danger-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                            }
                        }
                    });
                    return false;
                }
            }
        });
        return false;
    });

$(document).on('click', '#lesson-unschedule', function () {
        $.ajax({
            url: '<?=Url::to(['unscheduled-lesson/reason-to-unschedule', 'UnscheduleLesson[lessonIds]' => $model->id,'UnscheduleLesson[isBulk]' => false]);?>',
            type: 'get',
            success: function (response)
            {
                if (response.status)
                {
                    $('#menu-shown').hide();
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    // $('#success-notification').html(response.message).fadeIn().delay(3000).fadeOut();
                    // $.pjax.reload({container: "#lesson-schedule-buttons", replace: false, async: false, timeout: 6000});
                    // $.pjax.reload({container: '#lesson-detail', replace: false, async: false, timeout: 6000});
                    // if ($('#lesson-more-action').length) {
                    //     $.pjax.reload({container: "#lesson-more-action", replace: false, async: false, timeout: 6000});
                    // }
                    // $('#attendance-panel').hide();
                    // $('#loader').hide();
                } else {
                    $('#menu-shown').hide();
                    $('#error-notification').html(response.message).fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });

    $(document).off('click', '#credit-transfer').on('click', '#credit-transfer', function () {
        $('#loader').show();
        $.ajax({
            url: '<?=Url::to(['lesson/credit-transfer', 'id' => $model->id]);?>',
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                $('#loader').hide();
                if (response.status)
                {
                    $('#success-notification').html(response.message).fadeIn().delay(3000).fadeOut();
                    $.pjax.reload({container: "#lesson-payment-listing", replace: false, async: false, timeout: 6000});
                    $.pjax.reload({container: "#lesson-schedule-buttons", replace: false, async: false, timeout: 6000});
                } else {
                    $('#error-notification').html(response.message).fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });

    $(document).off('click', '#lesson-discount, .group-lesson-discount').on('click', '#lesson-discount, .group-lesson-discount', function(){
        var message = 'Warning: You have entered a non-approved Arcadia discount. All non-approved discounts must be submitted in writing and approved by Head Office prior to entering a discount, otherwise you are in breach of your agreement.';
        $('#modal-popup-warning-notification').text(message).fadeIn();
        if ($(this).attr('action-url')) {
            var url = $(this).attr('action-url');
        } else {
            var lessonId = '<?=$model->id;?>';
            var lessonIds = [lessonId];
            var params = $.param({ 'LessonDiscount[ids]': lessonIds });
            var url = '<?=Url::to(['private-lesson/apply-discount'])?>?' + params;
        }
        $.ajax({
            url    : url,
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).off('click', '#lesson-price').on('click', '#lesson-price', function(){
        $.ajax({
            url    : '<?=Url::to(['lesson/edit-price', 'id' => $model->id])?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                }
            }
        });
        return false;
    });

    $(document).off('click', '#edit-lesson-tax').on('click', '#edit-lesson-tax', function(){
        $.ajax({
            url    : '<?=Url::to(['lesson/edit-tax', 'id' => $model->id])?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                } else {
                    $('#lesson-view-tax').text(response.errors).fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });

    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({container: "#lesson-payment-listing", replace: false, async: false, timeout: 6000});
        $.pjax.reload({container: "#lesson-schedule-buttons", replace: false, async: false, timeout: 6000});
        return false;
    });

    $(document).on('click', '.edit-lesson-detail', function () {
        var customUrl = '<?=Url::to(['lesson/edit-classroom', 'id' => $model->id]);?>';
        $.ajax({
            url    : customUrl,
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });
</script>
