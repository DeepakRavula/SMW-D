<?php

use common\models\Program;
use backend\models\search\ProgramSearch;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = 'Programs';
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'new-program']);
$this->params['show-all'] = $this->render('_button', [
	'searchModel' => $searchModel
 ]);
?>
<div class="m-b-10">
</div>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="row">
    <div class="col-md-12">
        <?php
        echo $this->render('_index-private',
            [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        ?>
    </div>
</div>
<div>
<div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Program</h4>',
    'id' => 'program-modal',
]);
?>
<div id="program-content"></div>
<?php Modal::end(); ?>
</div>
<?php echo Html::hiddenInput('name','1',array('id'=>'program-type')); ?>
<script>
    $(document).ready(function () {
        $(document).on('click', '.action-button',function () {
            var type=$('#program-type').val();
                var customUrl = '<?= Url::to(['program/create']); ?>?type=' + type;
            $.ajax({
                url: customUrl,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#program-content').html(response.data);
                        $('#program-modal').modal('show');
                    }
                }
            });
        });
        $(document).on('click', '#private-program-grid tbody > tr',function () {
                var programId = $(this).data('key');
                var customUrl = '<?= Url::to(['program/update']); ?>?id=' + programId;
            $.ajax({
                url: customUrl,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#program-content').html(response.data);
                        $('#program-modal').modal('show');
                    }
                }
            });
        });
        
        $(document).on('beforeSubmit', '#program-form', function () {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status) {
                        var showAllPrograms = $(this).is(":checked");
                        var type=$('#program-type').val();
                        var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[type]=' + type;
                        $.pjax.reload({url: url, container: "#program-listing", replace: false, timeout: 4000});
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
	    $(document).on('click', '.private', function() {
            var type=$('#program-type').val('1');
		    $(".group").removeClass('active');		
		    $(".private").addClass('active');	
    	});
	    $(document).on('click', '.group', function() {
            var type=$('#program-type').val('2');
		    $(".private").removeClass('active');	
		    $(".group").addClass('active');	
	    });
	    $(document).on('click', '.group, .private', function() {
		    var type = $(this).attr('value');
		    var url = "<?php echo Url::to(['/program/index']); ?>?ProgramSearch[type]=" + type;
		    $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  
		    return false;
    	});
        $("#programsearch-showallprograms").on("change", function() {
            var type=$('#program-type').val();
            var showAllPrograms = $(this).is(":checked");
            var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms);
            $.pjax.reload({url:url,container:"#program-listing",replace:false,  timeout: 4000});  //Reload GridView
        });
        $("#programsearch-showallprograms").on("change", function () {
            var showAllPrograms = $(this).is(":checked");
            var type=$('#program-type').val();
            var url = "<?php echo Url::to(['program/index']); ?>?ProgramSearch[showAllPrograms]=" + (showAllPrograms | 0) + '&ProgramSearch[type]=' + type;
            $.pjax.reload({url: url, container: "#program-listing", replace: false, timeout: 4000});  //Reload GridView
        });
    });
</script>
