<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use common\models\Course;
?>

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
<div class="grid-row-open">
<?php Pjax::begin([
    'timeout' => 6000,
]) ?>
    <div class = "row">
        <div class = "col-md-6">
            <?php
            LteBox::begin([
                'type' => LteConst::TYPE_DEFAULT,
                'title' => 'Customer',
                'withBorder' => false,
            ])
            ?>
                <dl class="dl-horizontal">
                    <dt>Original</dt>
                    <dd><?= $model->publicIdentity; ?></dd>
                    <dt>Duplicate</dt>
                    <dd><?= $mergeUserModel->publicIdentity; ?></dd>
                </dl>
            <?php LteBox::end() ?>    
        </div>
        <div class = "col-md-6">
        <?php
            LteBox::begin([
                'type' => LteConst::TYPE_DEFAULT,
                'title' => 'Students',
                'withBorder' => false,
            ])
            ?>
            <dl class="dl-horizontal">
                    <?php
                    foreach ($mergeUserModel->students as $student) { ?>
                    <dd> <?= $student->fullName; ?> </dd>
                    <?php } ?>
            </dl>
        <?php LteBox::end() ?>    
        </div>
    </div>    
    <div class = "row">
        <div class = "col-md-12">
            <?php
            LteBox::begin([
                'type' => LteConst::TYPE_DEFAULT,
                'title' => 'Enrolments',
                'withBorder' => false,
            ])
            ?>
                <?php echo  $this->render('_enrolment', [
        'enrolmentDataProvider' => $enrolmentDataProvider,
    ]); ?>
        <?php LteBox::end() ?>    
        </div>
    </div>    
<?php Pjax::end(); ?>        
</div>
<div class="clearfix"></div>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Customer Merge Preview</h4>');
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#modal-popup-warning-notification').html('Merging another customer will delete all of their contact data. This can not be undone.').fadeIn();
        $('#modal-apply').text('Confirm');
        $('#modal-apply').show();  
    });

     $(document).off('click', '#modal-apply').on('click', '#modal-apply', function() {
        $('#modal-spinner').show();
        var customerId = '<?= $mergeUserModel->id; ?>';
        var params = $.param({customerId: customerId });
                    $.ajax({
                        url    : '<?= Url::to(['customer/merge', 'id' => $model->id]); ?>&'+params,
                        type   : 'post',
                        dataType: "json",
                        data   : $(this).serialize(),
                        success: function(response)
                        {
                            if (response.status) {
                                $('#modal-spinner').hide();
                                $('#popup-modal').modal('hide');  
                                bootbox.alert(response.message);                   
                            }
                            else {
                                $('#modal-spinner').hide();
                                $('#error-notification').html(response.errors).fadeIn().delay(8000).fadeOut();
                            }
                        }
                    });
        return false;
    });

</script>