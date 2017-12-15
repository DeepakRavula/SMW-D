<?php

use common\models\Program;
use backend\models\search\ProgramSearch;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Programs';
?>

<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="row">
    <div class="col-md-12">
	 <?php 
echo $this->render('_index-private', [
    'model' => $model,
    'searchModel' => $searchModel,
    'privateDataProvider' => $privateDataProvider,
]);

?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
	 <?php 
echo $this->render('_index-group', [
    'model' => $model,
    'searchModel' => $searchModel,
    'privateDataProvider' => $groupDataProvider,
]);

?>
    </div>
</div>   

<div class="clearfix"></div>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Program</h4>',
        'id' => 'program-modal',
    ]); ?>
<div id="program-content"></div>
 <?php  Modal::end(); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#add-program, #group-program-listing tbody > tr,#private-program-listing tbody > tr', function () {
            var programId = $(this).data('key');
            if (programId === undefined) {
                var customUrl = '<?= Url::to(['program/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['program/update']); ?>?id=' + programId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#program-content').html(response.data);
                        $('#program-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#program-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#program-listing', timeout: 6000});
                        $('#program-modal').modal('hide');
                    } else {
						$('#error-notification').html(response.message).fadeIn().delay(8000).fadeOut();
                        $('#program-modal').modal('hide');
					}

                }
            });
            return false;
        });
        $(document).on('click', '.program-cancel', function () {
            $('#program-modal').modal('hide');
            return false;
        });
    });
</script>
